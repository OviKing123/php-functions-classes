<?php

!class_exists( 'BingTranslate' ) && file_exists( $_SERVER['DOCUMENT_ROOT'] . '/@/classes/BingTranslate/BingTranslate.php' ) && require_once( $_SERVER['DOCUMENT_ROOT'] . '/@/classes/BingTranslate/BingTranslate.php' );
!class_exists( 'BingTranslate' ) && file_exists( $_SERVER['DOCUMENT_ROOT'] . '/@/classes/BingTranslate.php' ) && require_once( $_SERVER['DOCUMENT_ROOT'] . '/@/classes/BingTranslate.php' );
!class_exists( 'BingTranslate' ) && file_exists( $_SERVER['DOCUMENT_ROOT'] . '/classes/BingTranslate/BingTranslate.php' ) && require_once( $_SERVER['DOCUMENT_ROOT'] . '/classes/BingTranslate/BingTranslate.php' );
!class_exists( 'BingTranslate' ) && file_exists( $_SERVER['DOCUMENT_ROOT'] . '/classes/BingTranslate.php' ) && require_once( $_SERVER['DOCUMENT_ROOT'] . '/classes/BingTranslate.php' );
!class_exists( 'BingTranslate' ) && file_exists( dirname( __FILE__ ) . '/BingTranslate.php' ) && require_once( dirname( __FILE__ ) . '/BingTranslate.php' );
!class_exists( 'BingTranslate' ) && die( '<h1>BingTranslate class is needed to work.</h1>' );

$source_language = 'en';
$target_language = 'pt';
$original_text = "Face the challenge once again in this sequel to the hardcore action RPG. Dark Souls II features a new hero, a new storyline, and an unfamiliar world for players to survive in while delivering its signature brand of unrelenting punishment that players hunger for. Epic battles with gruesome enemies and blood-thirsty bosses await adventurers as they traverse through a devastated world filled with death and despair; only those who are strong willed or cunning can survive this journey to achieve great glory. Players seeking to share their experience will discover a revamped server-based multiplayer mode that puts a distinct Dark Souls II twist on the concept of playing with others.";

$bt = new BingTranslate();
$bt->setFrom( $source_language );
$bt->setTo( $target_language );
$bt->setText( $original_text );
$bt->run();

echo '<pre>';
var_dump( $bt );
echo '</pre>';

echo '<pre>' . $bt->getTranslation() . '</pre>';
die();

?>
