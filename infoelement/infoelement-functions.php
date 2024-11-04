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
		),
		$atts,
		'infoelement'
	);

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
            $r .= '<ul>'.justification_options($datapoint->value).'</ul>';
        }
    }

	return '<div class="infoelement">'.$r.'</div>';

}

function justification_options($data) {
    $r = '';
    foreach ($data as $datapoint) {
        if ($datapoint->show) {
            $r .= '<li class="active">'.$datapoint->section->en.'</li>';
        } else {
            $r .= '<li style="color: #ccc;">'.$datapoint->section->en.'</li>';
        }
    }
    return $r;
}