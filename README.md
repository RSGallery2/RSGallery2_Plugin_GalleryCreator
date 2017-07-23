# RSGallery2_Plugin_GalleryCreator
RSGallery2 plugin to automatically create a personal gallery for each "new" user that is created.
For web pages which want to support Rsgallery2 function "My Galleries". 

When a new user registers, this plugin (when enabled) will create a new RSGallery2 gallery for that new user and owned by that new user, based on the settings in the plugin parameters

When my galleries is activated in the RSGallery2 configuration then the user owned galleries will be shown on the site.

The user needs permissions
* 'edit own' and
* 'edit 'state own'
* 'delete own',
* ('create own'),
to upload/release and delete images in owned gallery.

'Create own' allows to add more galleries to the owned ones.  

Tip: Create a own user group for "myGallery" user and assign it for new users automatically.
A new user default group can be selected in  Backend > Users > User Manager > Options > Tab: User Options > Setting: New User Registration Group. Here select the created new user group.  

Install the plugin, *enable the plugin*, check the plugin settings and add a new user (try from frontend and backend).
