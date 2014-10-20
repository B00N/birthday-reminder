<?php
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_MMMenu                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Daniel Khan <dk@webcluster.at>                              |
// +----------------------------------------------------------------------+
// $Id: MMMenu.php,v 1.7 2005/10/25 22:31:45 datenpunk Exp $
// {{{ DB_NestedSet_MMMenu:: class
/**
 * Simple helper class which takes a node array create by DB_NestedSet and transforms it
 * into an array useable by HTML_Menu
 *
 * @see docs/Menu_example.php
 * @author Daniel Khan <dk@webcluster.at>
 * @package DB_NestedSet
 * @version $Revision: 1.7 $
 * @access public
 * @modification Frank Meyerer
 */
// }}}
/**
 * DB_NestedSet_MMMenu
 *
 * @package
 * @author daniel
 * @copyright Copyright (c) 2004
 * @version $Id: MMMenu.php,v 1.7 2005/10/25 22:31:45 datenpunk Exp $
 * @access public
 * @modification Frank Meyerer
 */
class DB_NestedSet_MMMenu extends DB_NestedSet_Output {
    // {{{ properties
    var $_structMenu = array();

    /**
     *
     * @var array Default field mappings
     * @access private
     */
    var $_paramDefaults = array(
    		'textField' => 'text',
        'titleField' => 'name',
        'urlField' => 'url',
        'mainIdField' => 'main_id',
		    'order_num' => 'norder',
		    'level'     => 'level',
		    'nav_safename' => 'nav_safename',
		    'nav_desc'     => 'nav_desc',
		    'content_id'   => 'content_id',
		    'meta_title' => 'meta_title',
		    'meta_desc'  => 'meta_desc',
		    'meta_keyw'  => 'meta_keyw',
		    'page_headline' => 'page_headline',
		    'page_copytext' => 'page_copytext',
		    'aktiv' => 'aktiv',
		    'deletable' => 'deletable',
		    's_page'    => 's_page',    
		    'start_id'  => 'start_id',
		    'logic' => 'logic',
		    'template' => 'template'
        );
    // }}}
    // {{{ DB_NestedSet_MMMenu
    /**
     * The constructor
     *
     * @param array $params The config parameters used for building the array.
     * @see _createFromStructure
     * @access public
     * @return void
     */
    function & DB_NestedSet_MMMenu($params) {
        $this->_structMenu = & $this->_createFromStructure($params);
    }
    // }}}
    // {{{ _createFromStructure
    /**
     * DB_NestedSet_MMMenu::_createFromStructure()
         * Creates the HTML_Menu Structure
         *
     * <pre>
         * @param array $params The configuration parameters.  Available
     *                          params are:
     * o 'structure'            => [REQU] The result from $nestedSet->getAllNodes(true)
     * o 'titleField'           => [REQU] The field in the table that has the text for node
     * o 'urlField'             => [REQU] The field in the table that has the link for the node
         * </pre>
     * @param $params
     * @return array The menu array
     **/
    function _createFromStructure($params, $iterate = false) {
        static $menuStructure, $menuParts, $offset;

        if(!$iterate) {
            $menuStructure = array();
            $menuParts = array();
            $offset = false;
        }

        // always start at level 1
        if (!isset($params['currentLevel'])) {
            $params['currentLevel'] = 1;
        }

        if (!isset($params['currentParent'])) {
            $params['currentParent'] = false;
        }

        if (!isset($menuParts)) {
            $menuParts = array();
        }

        if (!isset($menuStructure)) {
            $menuStructure = array();
        }

        // Set the default field mappings if not set in userland
        if (!isset($params['defaultsSet'])) {
            $this->_setParamDefaults($params);
        }
        // have to use a while loop here because foreach works on a copy of the array and
        // the child nodes are passed by reference during the recursion so that the parent
        // will know when they have been hit.

        reset($params['structure']);
        while(list($nodeID,$node)=each($params['structure'])) {
            // see if we've already been here before
            if (isset($node['hit']) || $node['level'] < $params['currentLevel']) {
                continue;
            }
            // mark that we've hit this node
            $params['structure'][$nodeID]['hit'] = $node['hit'] = true;

            // We are at a rootnode - let's add it to the structure
            if (empty($offset) || $offset==$node['level']) {
                $offset = $node['level'];
                $menuStructure[$node['id']] = array(
                    'title' => isset($node[$params['titleField']]) ? $node[$params['titleField']] : false,
                    'url' => isset($node[$params['urlField']]) ? $node[$params['urlField']] : false,
                    'main_id' => isset($node[$params['mainIdField']]) ? $node[$params['mainIdField']] : false,                    
                    'norder' => isset($node[$params['order_num']]) ? $node[$params['order_num']] : false,
                    'level' => isset($node[$params['level']]) ? $node[$params['level']] : false,
                    'nav_safename' => isset($node[$params['nav_safename']]) ? $node[$params['nav_safename']] : false,
                    'nav_desc' => isset($node[$params['nav_desc']]) ? $node[$params['nav_desc']] : false,
                    'content_id' => isset($node[$params['content_id']]) ? $node[$params['content_id']] : false,
                    'meta_title' => isset($node[$params['meta_title']]) ? $node[$params['meta_title']] : false,
                    'meta_desc' => isset($node[$params['meta_desc']]) ? $node[$params['meta_desc']] : false,
                    'meta_keyw' => isset($node[$params['meta_keyw']]) ? $node[$params['meta_keyw']] : false,
                    'page_headline' => isset($node[$params['page_headline']]) ? $node[$params['page_headline']] : false,
                    'page_copytext' => isset($node[$params['page_copytext']]) ? $node[$params['page_copytext']] : false,
                    'aktiv' => isset($node[$params['aktiv']]) ? $node[$params['aktiv']] : false,
                    'deletable' => isset($node[$params['deletable']]) ? $node[$params['deletable']] : false,
                    's_page' => isset($node[$params['s_page']]) ? $node[$params['s_page']] : false,
                    'start_id' => isset($node[$params['start_id']]) ? $node[$params['start_id']] : false,
                    'logic' => isset($node[$params['logic']]) ? $node[$params['logic']] : false,
                    'template' => isset($node[$params['template']]) ? $node[$params['template']] : false
                    );

                // Use a reference so we can happily modify $menuParts to change $menuStructure
                $menuParts[$node['id']] = & $menuStructure[$node['id']];
            }

            // Perform action for non-root nodes
            $currentParent = & $params['currentParent']['id'];
            if ($currentParent && isset($menuParts[$currentParent])) {
                $currentPart = & $menuParts[$currentParent]['sub'];
                $currentPart[$node['id']] = array(
                    'title' => isset($node[$params['titleField']]) ? $node[$params['titleField']] : false,
                    'url' => isset($node[$params['urlField']]) ? $node[$params['urlField']] : false,
                    'main_id' => isset($node[$params['mainIdField']]) ? $node[$params['mainIdField']] : false,                    
                    'norder' => isset($node[$params['order_num']]) ? $node[$params['order_num']] : false,
                    'level' => isset($node[$params['level']]) ? $node[$params['level']] : false,
                    'nav_safename' => isset($node[$params['nav_safename']]) ? $node[$params['nav_safename']] : false,
                    'nav_desc' => isset($node[$params['nav_desc']]) ? $node[$params['nav_desc']] : false,
                    'content_id' => isset($node[$params['content_id']]) ? $node[$params['content_id']] : false,
                    'meta_title' => isset($node[$params['meta_title']]) ? $node[$params['meta_title']] : false,
                    'meta_desc' => isset($node[$params['meta_desc']]) ? $node[$params['meta_desc']] : false,
                    'meta_keyw' => isset($node[$params['meta_keyw']]) ? $node[$params['meta_keyw']] : false,
                    'page_headline' => isset($node[$params['page_headline']]) ? $node[$params['page_headline']] : false,
                    'page_copytext' => isset($node[$params['page_copytext']]) ? $node[$params['page_copytext']] : false,
                    'aktiv' => isset($node[$params['aktiv']]) ? $node[$params['aktiv']] : false,
                    'deletable' => isset($node[$params['deletable']]) ? $node[$params['deletable']] : false,
                    's_page' => isset($node[$params['s_page']]) ? $node[$params['s_page']] : false,
                    'start_id' => isset($node[$params['start_id']]) ? $node[$params['start_id']] : false,
                    'logic' => isset($node[$params['logic']]) ? $node[$params['logic']] : false,
                    'template' => isset($node[$params['template']]) ? $node[$params['template']] : false
                    );
                $menuParts[$node['id']] = & $currentPart[$node['id']];
            }
            // see if it has children
            if (($node['r'] - 1) != $node['l']) {
                $children = array();
                // harvest all the children
                $tempStructure = $params['structure'];
                foreach ($tempStructure as $childKey => $childNode) {
                    if (!isset($childNode['hit']) && $node['rootid'] == $childNode['rootid'] && $node['l'] < $childNode['l'] && $node['r'] > $childNode['r'] && $childNode['level'] > $params['currentLevel']) {
                        // important that we assign it by reference here, so that when the child
                        // marks itself 'hit' the parent loops will know
                        $children[$childKey] = & $params['structure'][$childKey];
                    }
                }

                $recurseParams = $params;
                $recurseParams['structure'] = $children;
                $recurseParams['currentLevel']++;
                $recurseParams['currentParent'] = & $node;
                $this->_createFromStructure($recurseParams, true);
            }
        }
        return $menuStructure;
    }
    // }}}
    // {{{ returnStructure
    /**
     * DB_NestedSet_MMMenu::returnStructure()
     *
     * Returns an array suitable for HTML_Menu
     *
     * @return array An array useable for HTML_Menu
     */
    function returnStructure() {
        return $this->_structMenu;
    }
    // }}}
    // {{{ _setParamDefaults()
    /**
     * DB_NestedSet_MMMenu::_setParamDefaults()
     *
     * @param  $params Param array passed from userland
     * @return bool True on completion
     * @access private
     */
    function _setParamDefaults(& $params) {
        $defaults = $this->_paramDefaults;
        foreach($defaults AS $fieldName => $fieldAlias) {
            if (!isset($params[$fieldName])) {
                $params[$fieldName] = $fieldAlias;
            }
        }
        $params['defaultsSet'] = true;
        return true;
    }
    // }}}
}
?>