<?php

namespace Frontender\Platform\Routes\Api;

use Frontender\Core\Routes\Helpers\CoreRoute;
use Frontender\Core\Routes\Middleware\TokenCheck;
use Slim\Http\Request;
use Slim\Http\Response;
use Frontender\Core\DB\Adapter;

class Members extends CoreRoute {
	protected $group = '/api/members';

	protected function registerCreateRoutes() {
		parent::registerCreateRoutes();

		$this->app->put( '/add', function ( Request $req, Response $res ) {
			$data = $req->getParsedBody();

			if ( ! isset( $data['group'] ) || ! isset( $data['user_id'] ) || ! $data['group'] || ! $data['user_id'] ) {
				return $res->withStatus( 400 );
			}

			// Check if the team is present, if not create it.
			Adapter::getInstance()->collection( 'teams' )->findOneAndUpdate( [
				'name' => $data['group'],
			], [
				'$addToSet' => [ 'users' => (int) $data['user_id'] ]
			], [
				'upsert' => true
			] );

			if ( isset( $data['role'] ) && $data['role'] ) {
				Adapter::getInstance()->collection( 'roles' )->findOneAndUpdate( [
					'name' => $data['role']
				], [
					'$addToSet' => [ 'users' => (int) $data['user_id'] ]
				], [
					'upsert' => true
				] );
			}

			return $res->withStatus( 200 );
		} );
	}

	protected function getGroupMiddleware() {
		return [
			new TokenCheck(
				$this->app->getContainer()
			)
		];
	}
}
