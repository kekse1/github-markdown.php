![https://kekse.biz/github.php?draw&override=github:github-markdown"](github-markdown)


# v**0.2.0**
Uses the GitHub API to convert a `.md` Markdown document (like any `README.md`) to pure HTML.

## Reason
The reason for this script is this: first I wanted to embed the HTML code via JavaScript `fetch()`,
but as I wanted to use the GitHub API to convert from `.md`, the HTTP API request would need my own
token, which 'd be visible in the JavaScript code.

So I just needed a PHP script which hides the token and the whole request, etc.

## _**UPDATE**_
_It seems the API conversion itself also works without an Auth-TOKEN, so I can solve it in plain
JavaScript, too!_ So this script is not really important any longer; use it as reference if you want.

## Download
* [github-markdown.php](php/github-markdown.php)

## Usage
Just use the `\kekse\getMarkdownHTML()` function, it'll **return** the HTML code.

## Configuration
I put some `define()` into the `github-markdown.inc.php`, which needs to be in
the same directory as the script itself.

So it's easier to update the script to newer versions.

## Dependencies
The `cURL` PHP module. Should be installed most times, but if not, on Debian/Linux it's enough to
just `apt install php-curl`.

## TODO
I'm going to integrate a function so PHP will directly output the HTML code, but that's not _that_
important (for now).

# Copyright and License
The Copyright is [(c) Sebastian Kucharczyk](COPYRIGHT.txt),
and it's licensed under the [MIT](LICENSE.txt) (also known as 'X' or 'X11' license).

![kekse.biz](favicon.png)
