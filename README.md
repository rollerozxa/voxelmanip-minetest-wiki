# wikiozxa (Voxelmanip Wiki)
The version of Wikiozxa that used to power The Minetest Voxelmanip Wiki.

Compared to original wikiozxa, pages are stored in the filesystem which can be put into a Git repository to allow editing and contributing from a Git forge.

## Set-up notes
Requires some environment with PHP available, install Composer dependencies, compile SCSS stylesheets, import the database dump, all of that stuff...

The software expects requests to first check in `/static/`, and then go through the router script `/index.php`. You will need to use URL rewriting to make this happen. This is an example for how I do it, using nginx:

```nginx
location / {
	try_files /static$uri /index.php?$args;
}
location /index.php {
	# include your PHP fastcgi snippet file
}
```
