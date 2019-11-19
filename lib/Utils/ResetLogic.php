<?php

namespace Frontender\Platform\Utils;

/**
 * This class will hold all the reset logic and my notes for this functionality.
 *
 * How do we go about permissions?
 * Simple, the user which call php is also the user executing it.
 *
 * How will it work, we will call the mongorestore binary, which is install in $PATH.
 * If not preset we will show a notification.
 *
 * If found we will give the path and the --drop so that the collections are dropped before they are restored.
 * Collections not in the backup will not be dropped!
 */

class ResetLogic {
	public static function resetMongo( $event ) {
		$rootDir = dirname(__DIR__, 2);
		$mongoRestorePath = trim(`which mongorestore`);
		$restorePath = $rootDir . '/mongo-restore';

		if(!$mongoRestorePath) {
			throw new \Error('mongorestore isn\'t installed');
		}

		if(!file_exists($restorePath)) {
			throw new \Error('No backup directory found.');
		}

		shell_exec($mongoRestorePath . ' ' . $restorePath . ' --drop');

		echo 'All done!';
	}
}