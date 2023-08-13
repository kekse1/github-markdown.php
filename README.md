<img src="https://kekse.biz/php/count.php?draw&override=github:github-markdown&text=v4&draw" />

# v**0.1.1**
Uses the GitHub API to convert a `.md` Markdown document (like any `README.md`) to pure HTML.

## Reason
The reason for this script is this: first I wanted to embed the HTML code via JavaScript `fetch()`,
but as I wanted to use the GitHub API to convert from `.md`, the HTTP API request would need my own
token, which 'd be visible in the JavaScript code.

So I just needed a PHP script which hides the token and the whole request, etc.

## Download
* [github-markdown.php](php/github-markdown.php)

## Usage
Just use the `\kekse\getMarkdownHTML()` function, it'll **return** the HTML code.

## Dependencies
The `cURL` PHP module. Should be installed most times, but if not, on Debian/Linux it's enough to
just `apt install php-curl`.

## TODO
I'm going to integrate a function so PHP will directly output the HTML code, but that's not _that_
important **for now, for me**.. TODO. ^\_^

