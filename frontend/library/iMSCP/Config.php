<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 * Copyright (C) 2010-2018 by Laurent Declercq <l.declercq@nuxwin.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * This class wraps the creation and manipulation of the iMSCP_Config_Handler objects
 *
 * <b>Important consideration:</b>
 *
 * This class implement the <i>Singleton design pattern</i>, so, each type of
 * {@link iMSCP_Config_Handler} objects are instantiated only once.
 *
 * If you want use several instances of an iMSCP_Config_Handler object (e.g: To
 * handle separate configuration parameters that are stored in another container such
 * as a configuration file linked to a specific plugin) you should not use this class.
 * Instead of this, register your own iMSCP_Config_Handler objects into the
 * Registry object to be able to use them from all contexts.
 *
 * <b>Usage example:</b>
 * <code>
 * $parameters = array('PARAMETER_NAME' => 'PARAMETER_VALUE');
 * Registry::set('My_ConfigHandler', new iMSCP_Config_Handler($parameters));
 *
 * // From another context:
 *
 * $my_cfg = Registry::get('My_ConfigHandler');
 * echo $my_cfg->PARAMETER_NAME; // PARAMETER_VALUE
 * </code>
 *
 * See {@link Registry} for more information.
 *
 * To resume, the iMSCP_Config class acts as a registry for the iMSCP_Config_Handler
 * objects where the registered values (that are iMSCP_Config_Handler objects) are
 * indexed by their class name.
 */
class iMSCP_Config
{
    /**#@+
     * iMSCP_Config_Handler class name
     */
    const
        ARR = 'iMSCP_Config_Handler',
        DB = 'iMSCP_Config_Handler_Db',
        FILE = 'iMSCP_Config_Handler_File',
        INI = false,
        XML = false,
        YAML = false;
    /**#@-*/

    /**
     * Array that contain references to {@link iMSCP_Config_Handler} objects
     * indexed by their class name
     *
     * @staticvar array
     */
    private static $_instances = [];

    /**
     * Wrapper for getter method of an iMSCP_Config_Handler object
     *
     * @see iMSCP_Config_Handler::get()
     * @param string $index Configuration parameter key name
     * @param string $className iMSCP_Config_Handler class name
     * @return mixed Configuration parameter value
     */
    public static function get($index, $className = self::FILE)
    {
        return self::getInstance($className)->get($index);
    }

    /**
     * Get a iMSCP_Config_Handler instance
     *
     * Returns a reference to a {@link iMSCP_Config_Handler} instance, only
     * creating it if it doesn't already exist.
     *
     * The default handler object is set to {@link iMSCP_Config_Handler_File}
     *
     * @throws iMSCP_Exception
     * @param string $className iMSCP_Config_Handler class name
     * @param mixed $params Parameters that are passed to iMSCP_Config_Handler
     *                      object constructor
     * @return iMSCP_Config_Handler An iMSCP_Config_Handler instance
     */
    public static function getInstance($className = self::FILE, $params = NULL)
    {
        if (!array_key_exists($className, self::$_instances)) {
            if ($className === false) {
                throw new iMSCP_Exception(
                    'The iMSCP_Config_Handler object is not implemented.'
                );
            } elseif (!class_exists($className, true)) {
                throw new iMSCP_Exception("The class $className is not reachable.");
            } elseif (!is_subclass_of($className, 'iMSCP_Config_Handler')) {
                throw new iMSCP_Exception(
                    'Only iMSCP_Config_Handler objects can be handled by the ' .
                    __CLASS__ . ' class!'
                );
            }

            self::$_instances[$className] = new $className($params);
        }

        return self::$_instances[$className];
    }

    /**
     * Wrapper for setter method of an iMSCP_Config_Handler object
     *
     * @see iMSCP_Config_Handler::set()
     * @param string $index Configuration parameter key name
     * @param mixed $value Configuration parameter value
     * @param string $className iMSCP_Config_Handler class name
     * @return void
     */
    public static function set($index, $value, $className = self::FILE)
    {
        self::getInstance($className)->set($index, $value);
    }

    /**
     * Wrapper for {@link iMSCP_Config_Handler::del()} method
     *
     * @see iMSCP_Config_Handler::del()
     * @param string $index Configuration parameter key name
     * @param string $className iMSCP_Config_Handler class name
     * @return void
     */
    public static function del($index, $className = self::FILE)
    {
        self::getInstance($className)->del($index);
    }
}
