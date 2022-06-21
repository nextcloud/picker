# Picker

This app provides an endpoint to pick a file, generate a public link and directly browse it.
It can be used to integrate Nextcloud in other apps such as video call software.

To allow your app to embed Nextcloud Picker in a frame (in web browsers),
set one or multiple domains with this occ command:

```
occ config:app:set picker allowed_frame_ancestor_domains --value "my.first.domain,my.second.domain"
```

### Generic use

The entry point URL is:

https://your.nextcloud.org/index.php/apps/picker/single-link

It will directly show a file picker and then let you choose the share permissions. Once you're done,
a public link will be generated and you will be redirected to it.

## Webex embedded app

Webex provides an app framework to allow embedding other apps. To embed the Nextcloud picker in Webex,
create an embedded app in [https://developer.webex.com](https://developer.webex.com) with those values:

* Valid domains: Put you Nextcloud domain there. For example, if your Nextcloud is accessible
  at `https://your.nextcloud.org` you may set `your.nextcloud.org` as a valid domain.
* Start page URL: `https://your.nextcloud.org/index.php/apps/picker/webex-single-link`
* In-meeting start page URL: Leave this field empty.

You will then be able to select your Webex Nextcloud embedded app in the Webex desktop client
(Windows and MacOS only, this is not supported by the GNU/Linux Webex desktop client).

During a call, press the "Applications" button at the bottom right of the screen.
Then select your Nextcloud embedded app. Webex will ask you if you want to share your personal information
or not. This is not necessary for using this app.
You will then see and browse your start page URL and once you are finished selecting a file,
setting the permissions and you click "Start collaborating",
you will see an "Open for all" button below the embedded app panel.
Once you click this button, every call participant will be able to browse the public link you just created.

If the file file you selected is supported by Nextcloud Text or Nextcloud Office or OnlyOffice,
you will be able to collaborate on this file in real time.

You can then press "Close for all" if you want to hide the app for all participants.
