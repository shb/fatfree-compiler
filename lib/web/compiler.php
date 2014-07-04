<?php

namespace Web;

// Minify is a specialized template compiler which adds minification

// Minify can render a file for a mime type
// if the specified $file is present it passed as is
// otherwise if $file.php is present it is rendered through View
// otherwise if $file.$extention is present it is rendered through Extension::render
// the result is saved as $file

class Compiler
{
	private $files = "SCRIPTS";
	
	public static function handler(
		$req,
		$args
	) {
		$inst = array_keys( $args )[1];
		$cmp = Compiler::instance( $inst );
		echo $cmp->render( $args[$inst] );
	}

	private static $instances = array();
	public static function instance (
		$inst
	) {
		if (empty( self::$instances[$inst] ))
			self::$instances[$inst] = new Compiler( $inst );
		return self::$instances[$inst];
	}

	public function __construct(
		$files = "SCRIPTS"
	) {
		$this->files = $files;
	}

	public function render(
		$file,
		$mime = null,
		$hive = null,
		$ttl = 0
	) {
		$f3 = \Base::instance();
		$web = \Web::instance();
		// Alias is the requested file basename minus any trailing extension
		$alias = explode( ".", basename( $file ))[0];
		$ui = $f3->get( "{$this->files}.UI" );		

		// File alias is defined
		if ($f3->exists( "{$this->files}.{$alias}" ))
		{	// Try to minified the aliased files
			return $web->minify( $f3->get( "{$this->files}.{$alias}" ), $mime, true, $ui );
		}
		else
		{	// Try to render the file from a template:
			// Repository of template files
			// If mime is not set try to get it from the file name
			if (empty( $mime ))
				$mime = $web->mime( $file );

			// Try to render the template
			$f3->set( "UI", $ui );
			$out = \Template::instance()->render( $file, $mime, $hive, $ttl );
			// Then depending on mime type
			switch ( $mime )
			{	// When the file is of a minifiable type [TODO: make this configurable?]
				case "application/x-javascript":
				case "application/javascript":
				case "text/javascript":
				case "text/css":
					// And we are in something like production
					if (!$f3->get( "DEBUG" ))
					{	// Then render the template and minify it
						$tmp = $f3->get( "TEMP" );
						$tmpfile = "{$tmp}Web_Compiler_{$this->files}_{$file}";
						file_put_contents( $tmpfile, $out);
						return $web->minify( $tmpfile, $mime );
						// (As per FatFree best practices, caching is left on the routing level)
					}
				default:	return $out;
			}
		}
	}

}
