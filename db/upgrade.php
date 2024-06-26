<?php

// This file keeps track of upgrades to
// the choicegroup module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_choicegroup_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2013070900) {

        if ($oldversion < 2012042500) {

            /// remove the no longer needed choicegroup_answers DB table
            $choicegroup_answers = new xmldb_table('choicegroup_answers');
            $dbman->drop_table($choicegroup_answers);

            /// change the choicegroup_options.text (text) field as choicegroup_options.groupid (int)
            $choicegroup_options =  new xmldb_table('choicegroup_options');
            $field_text =           new xmldb_field('text', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'choicegroupid');
            $field_groupid =        new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'choicegroupid');

            $dbman->rename_field($choicegroup_options, $field_text, 'groupid');
            $dbman->change_field_type($choicegroup_options, $field_groupid);

        }
        // Define table choicegroup to be created
        $table = new xmldb_table('choicegroup');

        // Adding fields to table choicegroup
        $newField = $table->add_field('multipleenrollmentspossible', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $dbman->add_field($table, $newField); 


        upgrade_mod_savepoint(true, 2013070900, 'choicegroup');
    }

    if ($oldversion < 2015022301) {
        $table = new xmldb_table('choicegroup');

        // Adding field to table choicegroup
        $newField = $table->add_field('sortgroupsby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $newField)) {
            $dbman->add_field($table, $newField);
        }

        upgrade_mod_savepoint(true, 2015022301, 'choicegroup');
    }

    if ($oldversion < 2021071400) {

        // Define field maxenrollments to be added to choicegroup.
        $table = new xmldb_table('choicegroup');
        $field = new xmldb_field('maxenrollments', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'sortgroupsby');

        // Conditionally launch add field maxenrollments.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Choicegroup savepoint reached.
        upgrade_mod_savepoint(true, 2021071400, 'choicegroup');
    }

    if ($oldversion < 2021080500) {

        // Define field onlyactive to be added to choicegroup.
        $table = new xmldb_table('choicegroup');
        $field = new xmldb_field('onlyactive', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'sortgroupsby');

        // Conditionally launch add field onlyactive.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Group choice savepoint reached.
        upgrade_mod_savepoint(true, 2021080500, 'choicegroup');
    }

    
    if ($oldversion < 2024010399) {

        // Define field onlyactive to be added to choicegroup.
        $table = new xmldb_table('choicegroup');
        $field = new xmldb_field('ablaufwahl', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'onlyactive');
        $field2 = new xmldb_field('th_one', XMLDB_TYPE_CHAR, '255',  null, XMLDB_NOTNULL, null, 'Gruppenwahl', 'ablaufwahl');
        $field3 = new xmldb_field('th_two', XMLDB_TYPE_CHAR, '255',  null, XMLDB_NOTNULL, null, 'Gruppe', 'th_one');
        $field4 = new xmldb_field('th_thre', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'Gruppenmitglieder', 'th_two');

        // Conditionally launch add field onlyactive.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }
        if (!$dbman->field_exists($table, $field3)) {
            $dbman->add_field($table, $field3);
        }
        if (!$dbman->field_exists($table, $field4)) {
            $dbman->add_field($table, $field4);
        }
        
        // Define new Table and Fields for Redirectlinks
        $tablecr = new xmldb_table('choicegroup_redirects');
        $fieldid = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $fieldcgid = new xmldb_field('choicegroupid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'id');
        $fieldgid = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'choicegroupid');
        $fieldrelink = new xmldb_field('redirectlink', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, 'www.google.de', 'groupid');

        if(!$dbman->table_exists($tablecr)){
            $tablecr->addField($fieldid);
            $tablecr->addField($fieldcgid);
            $tablecr->addField($fieldgid);
            $tablecr->addField($fieldrelink);


            $keyid = new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $keycgid = new xmldb_key('choicegroupid', XMLDB_KEY_FOREIGN, ['choicegroupid'], 'choicegroup', ['id']);

            $tablecr->addKey($keyid);
            $tablecr->addKey($keycgid);

            $dbman->create_table($tablecr);
        }
        
        // Group choice savepoint reached.
        upgrade_mod_savepoint(true, 2024010399, 'choicegroup');
    }

    return true;
}
