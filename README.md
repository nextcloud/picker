# Picker

This app provides an endpoint to pick a file, generate a public link and directly browse it.
It can be used to integrate Nextcloud in other apps such as video call software.

To allow your app to embed Nextcloud Picker, set one or multiple domains with this occ command:

```
occ config:app:set picker allowed_frame_ancestor_domains --value "my.first.domain,my.second.domain"
```
