<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define('URL_BASE', 'https://us-central1-ietd-9b2b6.cloudfunctions.net/getFrameworkInfoelement');
define('RECOMENDATION_OPTIONS', [
    'Strong recommendation against the option',
    'Conditional recommendation against the option',
    'Conditional recommendation for either the option or the comparison',
    'Conditional recommendation for the option',
    'Strong recommendation for the option'
]);

function infoelement_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'organisation' => '',
			'project' => '',
			'framework' => '',
            'url' => ''
		),
		$atts,
		'infoelement'
	);

    // check at least one of the attributes is set
    if (empty($atts['organisation']) && empty($atts['project']) && empty($atts['framework']) && empty($atts['url'])) {
        return '<div class="infoelement">Please provide at least one of the following attributes: organisation, project, framework or url</div>';
    }
    // evaluate if the url is empty and the organisation, project and framework have at least 20 characters
    if (empty($atts['url']) && (strlen($atts['organisation']) < 20 || strlen($atts['project']) < 20 || strlen($atts['framework']) < 20)) {
        return '<div class="infoelement">Please provide a valid organisation, project and framework</div>';
    }
    
    // evaluate if the url is valid and start with https://new-ietd-test.epistemonikos.org or https://new-ietd.epistemonikos.org
    if ($atts['url'] && !preg_match('/^https:\/\/new-ietd(-test)?.epistemonikos.org/', $atts['url'])) {
        return '<div class="infoelement">Please provide a valid url</div>';
    }

    if ($atts['url']) {
        $url_parts = explode('/', explode('?', $atts['url'])[0]);
        $atts['organisation'] = $url_parts[4];
        $atts['project'] = $url_parts[6];
        $atts['framework'] = $url_parts[8];
    }

    $response = wp_remote_get( URL_BASE . '?organisation='.$atts['organisation'].'&project='.$atts['project'].'&framework='.$atts['framework'] );
    $body     = wp_remote_retrieve_body( $response );
    
    $data = json_decode( $body );
    $r = '';

    foreach ( $data as $datapoint ) {
        if ( $datapoint->element_id === 'conclusion-recommendation') {
            $r .= '<h4>Conclusion Recommendation</h4>';
            $r .= '<p>'.$datapoint->value.'</p>';
        }
        if ( $datapoint->element_id === 'conclusion-recommendation-options') {
            $r .= '<h4>Conclusion Recommendation Options</h4>';
            $r .= '<p><b>'. RECOMENDATION_OPTIONS[$datapoint->value] .'</b></p>';
        }

        if ( $datapoint->element_id === 'conclusion-justification') {
            $r .= '<h4>Conclusion Justification</h4>';
            $r .= '<p>'.$datapoint->value.'</p>';
        }
        if ( $datapoint->element_id === 'conclusion-justifictaion-options') {
            $r .= '<h4>Conclusion Justification Options</h4>';
            $r .= '<ul>'.infoelement_justification_options($datapoint->value).'</ul>';
        }
    }

	return '<div class="infoelement">'.$r.'</div>';

}

function infoelement_justification_options($data) {
    $r = '';
    foreach ($data as $datapoint) {
        if ($datapoint->show) {
            $r .= '<li class="active">'.$datapoint->section->en.'</li>';
        } else {
            $r .= '<li>'.$datapoint->section->en.'</li>';
        }
    }
    return $r;
}