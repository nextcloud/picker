# Picker

This app provides a page to pick a file, generate a public link and directly browse it.
It can be used to integrate Nextcloud in other apps such as video call software.

To allow your app to embed Nextcloud Picker in a frame (in web browsers),
set one or multiple domains with this occ command:

```
occ config:app:set picker allowed_frame_ancestor_domains --value "my.first.domain,my.second.domain"
```

## **🛠️ State of maintenance**

While there are many things that could be done to further improve this app, the app is currently maintained with **limited effort**. This means:

- The main functionality works for the majority of the use cases
- We will ensure that the app will continue to work like this for future releases and we will fix bugs that we classify as 'critical'
- We will not invest further development resources ourselves in advancing the app with new features
- We do review and enthusiastically welcome community PR's

We would be more than excited if you would like to collaborate with us. We will merge pull requests for new features and fixes. We also would love to welcome co-maintainers.

If there is a strong business case for any development of this app, we will consider your wishes for our roadmap. Please [contact your account manager](https://nextcloud.com/enterprise/) to talk about the possibilities.

### Generic usage

The entry point URL is:

https://your.nextcloud.org/index.php/apps/picker/single-link

It will directly show a file picker and then let you choose the share permissions. Once you're done,
a public link will be generated and you will be redirected to it.

## Webex embedded app

Webex provides an app framework to embed other apps. To embed the Nextcloud Picker into Webex,
create an embedded app in [https://developer.webex.com](https://developer.webex.com) with those values:

* Valid domains: Put you Nextcloud domain there. For example, if your Nextcloud is accessible
  at `https://your.nextcloud.org` you may set `your.nextcloud.org` as a valid domain.
* Start page URL: `https://your.nextcloud.org/index.php/apps/picker/webex-single-link`
* In-meeting start page URL: Leave this field empty.

You will then be able to select your Webex Nextcloud embedded app in the Webex desktop client
(Windows and MacOS only, this is not supported by the GNU/Linux Webex desktop client).

During a call, press the "Applications" button at the bottom right of the screen.
Then select your Nextcloud embedded app. Webex will ask you if you want to share your personal information
or not. This is not required when using this app.
You will then see and browse your start page URL. Once you are finished selecting a file,
setting the permissions and you click "Start collaborating",
you will see an **"Open for all"** button below the embedded app panel.
Once you click this button, every call participant will be able to browse the public link you just created.

If the file file you selected is supported by Nextcloud Text, Nextcloud Office or OnlyOffice,
you will be able to collaborate on this file with all the call participants.

You can then press "Close for all" if you want to hide the app for all participants.

## Copy link to Clipboard and close the window/iframe

It is possible to launch a specific picker interface with a button that would close automatically after copying to the clipboard the desired link. To do so, you can either launch it in a new window with the following code : 
 
```javascript
newButton.onclick = () => {
  pickerWindow = window.open('https://example.com/index.php/apps/picker/single-link?option=Clipboard', 'pickerWindow', 'popup');
};
```
or launch it in an iframe with the following code : 
```javascript
newButton.onclick = () => {
  // Create the iframe element
  var pickerFrame = document.createElement('iframe');
  pickerFrame.id = 'pickerFrame';
  pickerFrame.src = 'https://example.com/index.php/apps/picker/single-link?option=Clipboard'; // Set the source URL
  pickerFrame.style.height = '800px'; // set the height
  pickerFrame.style.width = '800px'; // set the width
  pickerFrame.style.maxHeight = '80vh'; // set max height to display on mobile
  pickerFrame.style.maxWidth = '100%'; // set max width to display on mobile
  pickerFrame.style.border = 'none'; // Remove the default border
  // Set the iframe styles to position it in the middle and on top
  pickerFrame.style.position = 'fixed';
  pickerFrame.style.top = '50%';
  pickerFrame.style.left = '50%';
  pickerFrame.style.transform = 'translate(-50%, -50%)';
  pickerFrame.style.zIndex = 9999; // Adjust this value as needed
  // Append the iframe to the document body
  document.body.appendChild(pickerFrame);
};
// This method needs to be top-level to be accessible by the iframe via the parent window accessor.
function closePickerIframe() {
  const pickerFrame = document.getElementById('pickerFrame');
  document.body.removeChild(pickerFrame); // Remove the iframe
}
```
