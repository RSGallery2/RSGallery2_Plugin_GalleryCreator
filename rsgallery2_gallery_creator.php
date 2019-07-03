<?php
/**
 * RSGallery2 Gallery Creator plugin
 * This plugin creates a new gallery for a newly created user
 *
 * @version     4.0.3
 * @package        RSGallery2
 * @subpackage    Content plugin
 * @copyright    Copyright (C) 2013-2018 RSGallery2 Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @package        Joomla.Plugin
 * @subpackage    User.RSGallery2_gallery_creator
 */
class plgUserRsgallery2_gallery_creator extends JPlugin
{

    /**
     * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
     * If you want to support 3.0 series you must override the constructor
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Utility method to act on a user after it has been saved.
     *
     * Creates a gallery for RSGallery2 owned by the newly created user
     *
     * @param    array $user Holds the new user data.
     * @param    boolean $isnew True if a new user is stored.
     * @param    boolean $success True if user was succesfully stored in the database.
     * @param    string $msg Message. (don't know what kind of message)
     *
     * @return    void
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        try {
            // Initialise variables.
            $database = JFactory::getDBO();
            //$app	= JFactory::getApplication();
            //$app->isAdmin() is true when user created in backend, false when created in frontend

            // Plugin parameters
            $publish_gallery = $this->params->get('publish_gallery', 0);
            $parent_gallery = $this->params->get('gid', 0);

            // Only create a gallery for a new user
            if ($isNew) {
                //// Load user_joomla plugin language (not done automatically).
                //$lang = JFactory::getLanguage();
                //$lang->load('plg_user_rsgallery2_gallery_creator', JPATH_ADMINISTRATOR);

                // Initialise RSGallery2 component

                // Right on site login ?
                require_once(JPATH_ADMINISTRATOR . '/components/com_rsgallery2/init.rsgallery2.php');
                require_once(JPATH_ADMINISTRATOR . '/components/com_rsgallery2/options/galleries.class.php');//needed for new rsgGalleriesItem class

                // Create the set of details for our new gallery
                $row = new rsgGalleriesItem($database);
                $row->parent = $parent_gallery; // root for now
                $row->name = $user['username'];
                $row->alias = JFilterOutput::stringURLSafe($row->name);
                $row->published = $publish_gallery;
                $row->date = date('Y-m-d H:i:s');
                $row->ordering = ($row->getNextOrder('parent = ' . $row->parent));
                $row->uid = $user['id'];

                // How to handle the asset/permissions? Todo: (now asset from parent is used automatically)
                // Asset example:
                //	{"core.create":[],"rsgallery2.create.own":[],"core.delete":[],"rsgallery2.delete.own":[],"core.edit":[],"core.edit.own":[],"core.edit.state":[],"rsgallery2.edit.state.own":[],"rsgallery2.vote":[],"rsgallery2.comment":[]}

                if (!$row->check()) {
                    // JError::raiseWarning(500, JText::_('PLG_USER_RSGALLERY2_GALLERY_CREATOR_CHECK_FAILED'));
                    $msg = JText::_('PLG_USER_RSGALLERY2_GALLERY_CREATOR_CHECK_FAILED');
                    $app = JFactory::getApplication();
                    $app->enqueueMessage($msg, 'error');
                    return false;
                }
                if (!$row->store()) {
                    //JError::raiseWarning(500, JText::_('PLG_USER_RSGALLERY2_GALLERY_CREATOR_STORE_FAILED'));
                    $msg = JText::_('PLG_USER_RSGALLERY2_GALLERY_CREATOR_STORE_FAILED');
                    $app = JFactory::getApplication();
                    $app->enqueueMessage($msg, 'error');
                    return false;
                }

                $success = JText::sprintf('PLG_USER_RSGALLERY2_GALLERY_CREATOR_SUCCESS', $user['username']);
                JFactory::getApplication()->enqueueMessage($success, 'message');

                $row->checkin();
                $row->reorder();
            } else {
                // Existing user - nothing to do...yet.
                // ToDo: Perhaps check if the user owns a gallery, if not, create it ?
            }
        } catch (Exception $e) {
            $msg = JText::_('PLG_USER_RSGALLERY2_GALLERY_CREATOR') . ' Error:  (01)' . $e->getMessage();
            $app->enqueueMessage($msg, 'error');
        }
    }

    /**
     * Does nothing (yet): ToDo: perhaps do something with the user's gallery after the user is deleted?
     *
     * Method is called after user data is deleted from the database
     *
     * @param    array $user Holds the user data
     * @param    boolean $succes True if user was succesfully stored in the database
     * @param    string $msg Message
     *
     * @return    boolean
     */
    /*	public function onUserAfterDelete($user, $succes, $msg)
        {
            //Perhaps unpublish or delete the gallery belonging to the user?
            //Gallery name is the username: $user[username]
            JFactory::getApplication()->enqueueMessage('Message: user deleted');
            return true;
        }
    */
}