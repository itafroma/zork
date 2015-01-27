<?php

/**
 * @file
 * Primitive functions.
 *
 * Overall implementation notes:
 *  - Exceptions are closer to MDL's <ERROR> behavior and are used instead of
 *    trigger_error().
 */

namespace Itafroma\Zork;

use Itafroma\Zork\Exception\ConstantAlreadyDefinedException;
use Itafroma\Zork\Exception\FlagwordException;
use Itafroma\Zork\Exception\PsetgDuplicateException;
use \BadFunctionCallException;

/**
 * Defines a global constant, throwing an exception if it's already set.
 *
 * Effectively acts like a declare(), but elevates PHP's built-in E_NOTICE on
 * redefining constants to an exception.
 *
 * @param string $foo The constant name to set.
 * @param mixed  $bar The value to set $foo to.
 * @throws Itafroma\Zork\Exception\ConstantAlreadyDefinedException
 */
function msetg($foo, $bar) {
    global $zork;

    if (isset($zork[$foo]) && $bar !== constant($foo)) {
        throw new ConstantAlreadyDefinedException();
    }

    $zork[$foo] = $bar;
}

/**
 * Sets a global variable and adds its value to the global "pure_list" array.
 *
 * @param string $foo The global variable to set.
 * @param mixed  $bar The value to set $foo to.
 * @return mixed The value of $bar.
 * @throws Itafroma\Zork\Exception\PsetgDuplicateException
 */
function psetg($foo, $bar) {
    global $zork;

    $pl = [];
    $zork[$foo] = $bar;

    if (!isset($zork['PURE_LIST'])) {
        $zork['PURE_LIST'] = [];
    }
    $pl = $zork['PURE_LIST'];

    if (!in_array($zork[$foo], $pl)) {
        $pl = array_merge($zork[$foo], $pl);
        $zork['PURE_LIST'] = $pl;
    }
    elseif (!empty($zork['PURE_CAREFUL'])) {
        throw new PsetgDuplicateException();
    }

    return $bar;
}

/**
 * Creates a flag/bit field.
 *
 * In the original source, there's a hard limit of 36 flag that can be added to
 * the flag field, presumably done for memory conservation.
 *
 * @param string ... $fs A list of flags to add.
 * @return int The number of flags added.
 * @throws Itafroma\Zork\Exception\FlagwordException
 */
function flagword(...$fs) {
    global $zork;

    $tot = 1;
    $cnt = 1;

    // Given <FLAGWORD>'s usage in the rest of the original source, this could
    // be simplified to a simple foreach loop. The use of array_walk_recursive()
    // here is to emulate the use of MDL's <MAPF> SUBR in the original source.
    array_walk_recursive($fs, function($f) {
        // It's unknown what the GROUP_GLUE symbol is in the oblist. It appears
        // to be always empty.
        if (!isset($zork['OBLIST']['GROUP_GLUE'])) {
            msetg($f, $tot);
        }

        $tot *= 2;

        $cnt++;
        if($cnt > 36) {
            throw new FlagwordException();
        }
    });

    return $cnt;
}

/**
 * Generates a new structure.
 *
 * This function is intentionally left unimplemented. The purpose of it was to
 * emulate C's struct language feature. In PHP, struct emulation is done through
 * simple class declarations using public properties.
 *
 * @param string    $nam  The name of the structure.
 * @param string    $prim The underlying type of the structure.
 * @param mixed ... $elem A series of elements defining the structure,
 *                        alternating between the element name and the element
 *                        type.
 * @throws \BadFunctionCallException
 */
function newstruc($nam, $prim, ...$elem) {
    throw new BadFunctionCallException('newstruc() has been removed: implement Itafroma\Prim\Newstruc instead.');
}
