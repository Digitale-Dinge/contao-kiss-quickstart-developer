# contao-kiss-quickstart-developer

Documentation for the [contao-kiss quickstart](../contao-kiss-quickstart): the `component_showcase` page type, the
showcase templates and their styles. It is installed while a project still needs the docs and removed afterwards.

The showcase content itself lives in the quickstart database under the "Documentation" page. Placeholder images,
logos, audio and video are shared with the base pages and stay in `files/quickstart/placeholders/`. Contao does not
sync symlinked folders into `tl_files`, so that media cannot ship with this bundle.

The megamenu icons of the documentation pages (`cube`, `layers`, `puzzle`, `palette`) ship with this bundle in
`layout/icons/`. The pages store them by path (`vendor/digitaledinge/contao-kiss-quickstart-developer/layout/icons/…`),
so they leave together with the bundle. The backend icon picker only lists `layout/kiss_icons/svg/` of the quickstart
and does not offer them.

## Development

The quickstart requires this repository as a Composer path repository (`../contao-kiss-quickstart-developer`), so
edits here are live there. Its theme build imports `layout/css/showcase-nav.pcss` and `layout/css/showcase.pcss` from
`layout/css/components/index.pcss`.

## Removing the docs

1. In the backend, delete the page "Documentation" with its subpages. Do this first: the `component_showcase` page
   type, the `documentation_card` content element and the page icons come from this bundle, and the pages fail to
   render without them.
2. `composer remove digitaledinge/contao-kiss-quickstart-developer` and drop its path repository from `composer.json`.
3. Delete the two showcase `@import` lines from `layout/css/components/index.pcss` and run `npm run build`.
