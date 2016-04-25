<?php

namespace CoMa\Helper;

/**
 * Handle Wordpress Post Revision with Area and Component
 * Class Revesion
 * @package CoMa\Helper
 */
class Revision
{

    public static function init()
    {
        if (Base::getWPOption(\CoMa\WP\Options\USE_WP_PAGE_POST_REVISION)) {

            add_action('save_post', function ($postId) {

                $pageId = null;

                $revisionId = wp_is_post_revision($postId);
                if (!$revisionId) {
                    $revisions = wp_get_post_revisions($postId);
                    if (count($revisions) > 0) {
                        $pageId = array_shift($revisions)->ID;
                    }
                } else {
                    $pageId = $revisionId;
                }

                if (!$pageId)
                    return null;

                self::cloneComponents($postId, $pageId);

            });

            add_action('wp_restore_post_revision', function ($postId, $revisionId) {
                if (Base::getWPOption(\CoMa\WP\Options\USE_WP_PAGE_POST_REVISION)) {
                    \CoMa\Helper\Controller::removeControllerFromPage($postId);
                    self::cloneComponents($revisionId, $postId);
                }
            }, 10, 2);

        }

    }


    private static function cloneComponents($postId, $pageId)
    {

        $pageControllers = \CoMa\Helper\Controller::getControllersByPageId($postId);
        foreach ($pageControllers as $pageControllerData) {
            $controller = \CoMa\Helper\Controller::parseController($pageControllerData);
            $cloneController = \CoMa\Helper\Controller::cloneController($controller);
            $cloneController->setPageId($pageId);
            \CoMa\Helper\Controller::saveController(['id' => $cloneController->getId(), 'page_id' => $pageId]);
        }

    }

}

?>