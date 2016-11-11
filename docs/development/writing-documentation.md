Writing documentation
=====================

Documentation is written in Markdown in the `docs/` directories. It is also
compiled to a HTML version by [Couscous][couscous] and deployed
to GitHub Pages when the Travis build against master succeeds. This is
configured in this project's Travis configuration.

[couscous]: https://couscous.io/

To preview how the compiled documentation looks, run:
 
```sh-session
$ vendor/bin/couscous preview
```

When you add a new Markdown file, and would like it to be included in the
compiled documentation's navigation, add it to this project's Couscous
configuration in `/couscous.yml`. This configuration should speak for itself,
but more information can be found in the
[template's documentation](../couscous/template/README.md) or in
[Couscous' documentation][couscous].
