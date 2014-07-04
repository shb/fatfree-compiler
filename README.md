fatfree-compiler
================

A drop-in compiler/minifier for js/css (and others) files and templates in FatFree.


INSTALLATION
---

Clone or download the repository contents

Copy the contents of `lib/` inside FatFree installation `lib/` directory.

*or*

Copy the contents of `lib/` somewhere and add its path to FatFree AUTOLOAD paths.


USAGE
---

It makes possilbe to define aliases for batches of file files inside server's
configuration, or to render css and javascript as F3 templates.

You can define aliases and templates for files minification as:

    ALIASES=scripts/
    ALIASES.one=script1.js,script2.js

etc. `ALIASES` defined the directory where templates are found. Then you
can define aliases as array entries for the same variable.

In code you must instantiate a copy of the compiler for the files you have
configured, thus:

    $compiler = new \Compiler("ALIASES");
    // or
    $compiler = \Compiler::instance("ALIASES");

Doing so, you can instantiate different compilers for different types of files.

You can render a file:

    $compiler->render( "one.js" );

If an alias (without the extension) is defined, the output is the result
of the aliased files minification, from the directory defined as `ALIASES`.

If an alias is not present, then "one.js" is treated as a FatFree template
and rendered through Template::render(). If the file's mime type (as specified
or desumed from its name) is a minifiable one (css, or js), and `DEBUG` is
not true, then the output is minified before being returned.

In any case, the response content-type is set accordingly, and caching of
the output is left at the routing level, as per `\Template::render()` and
`\Web::minify()` behaviours.
