<?php

namespace Webhd\Helpers;

use DirectoryIterator;
use MatthiasMullie\Minify;

\defined( '\WPINC' ) || die;

class W {

    // --------------------------------------------------

    /**
     * @param $css
     * @param bool $debug_check
     *
     * @return string
     */
    public static function CSS_Minify( $css, bool $debug_check = true ): string {
        if ( empty( $css ) ) {
            return $css;
        }

        if ( true === $debug_check && WP_DEBUG ) {
            return $css;
        }

        $minifier = new Minify\CSS();
        $minifier->add( $css );

        return $minifier->minify();
    }

    // --------------------------------------------------

    /**
     * @param ?string $path - full-path dir
     * @param bool $required_path
     * @param bool $required_new
     * @param string $FQN
     * @param bool $is_widget
     *
     * @return void
     */
    public static function FQN_Load( ?string $path, bool $required_path = false, bool $required_new = false, string $FQN = '\\', bool $is_widget = false ) {
        if ( $path ) {
            $iterator = new DirectoryIterator( $path );
            foreach ( $iterator as $fileInfo ) {
                if ( $fileInfo->isDot() ) {
                    continue;
                }

                $filename    = File::fileName( $fileInfo, false );
                $filenameFQN = $FQN . $filename;

                if ( $required_path ) {
                    require $path . DIRECTORY_SEPARATOR . $filename . File::fileExtension( $fileInfo, true );
                }

                if ( $required_new ) {
                    if ( ! $is_widget ) {
                        class_exists( $filenameFQN ) && ( new $filenameFQN() );
                    } else {
                        class_exists( $filenameFQN ) && register_widget( new $filenameFQN() );
                    }
                }
            }
        }
    }

}