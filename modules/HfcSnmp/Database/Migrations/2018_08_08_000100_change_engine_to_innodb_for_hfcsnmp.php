<?php

/**
 * We first planned to use FULLTEXT indexes on our tables – so most of them are created using MyISAM.
 * This policy changed – we convert all tables to InnoDB.
 *
 * @author Patrick Reichel
 */
class ChangeEngineToInnodbForHfcsnmp extends ChangeEngineToInnodb
{
}
