Klasse-wp-poll-survey
=====================

With this open source plugin, you can create and distribute Polls & Surveys within your WordPress website.

Default are Poll and Personality test available, although it's possible to create multiple configurations. For every test, you can create multiple versions. Every version has the same amount of questions and answers, but you can rephrase them, based on the target audience. The admin page to manage a test is fully custom developed.

To let the target audience submit their entries, you have to put a shortcode in a post or page.
Based on your configuration, they will see an intro first. Page by page, they can answer the questions and on the last page, feedback will be provided.

Depending on your configuration, the result will be a graph, text or number.

## Contribute

### First time
1. Clone this repo
2. Open terminal in the klasse-wp-poll-survey folder
3. `nmp install` (if you don't have npm installed https://docs.npmjs.com/getting-started/installing-node)
4. `bower install` (if you don't have bower installed http://bower.io/)
5. `composer install` (if you don't have composer installed https://getcomposer.org/doc/00-intro.md)
6. `grunt githooks` (only once) This will install a git pre-commit hook that will do a `grunt build`
on git commit
### Developing
1. `grunt` this will run a watch file and build the css and js files in the assets folder