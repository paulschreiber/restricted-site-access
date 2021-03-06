<?php
/**
 * Multisite / Single Site test class
 *
 * @package restricted-site-access
 */

/**
 * PHPUnit test class
 */
class MultiSiteSingleSiteTest extends \TestCase {

	/**
	 * Test restricted access, send to the login screen option
	 */
	public function testRestrictLoginScreen() {
		$I = $this->openBrowserPage();

		$I->loginAs( 'wpsnapshots' );

		$this->networkActivate( $I );

		$this->setMultiSiteVisibilitySettings( $I,
			[
				'mode'       => 'rsa-mode-default',
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$this->setSiteVisibilitySettings( $I,
			[
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$I->logout();

		$I->moveTo( '/' );

		usleep( 500 );

		$contains = false;

		if ( false !== strpos( $I->getCurrentUrl(), 'wp-login.php' ) ) {
			$contains = true;
		}

		$this->assertTrue( $contains );
	}

	/**
	 * Test restricted access, send to a specified web address option
	 */
	public function testRestrictWebAddress() {
		$I = $this->openBrowserPage();

		$I->loginAs( 'wpsnapshots' );

		$this->networkActivate( $I );

		$this->setMultiSiteVisibilitySettings( $I,
			[
				'mode'       => 'rsa-mode-default',
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$this->setSiteVisibilitySettings( $I,
			[
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-redirect-visitor',
			],
			[
				[
					'field' => 'redirect',
					'value' => 'https://www.google.com/',
					'type'  => 'input',
				],
			]
		);

		$I->logout();

		$I->moveTo( '/' );

		usleep( 500 );

		$this->assertTrue( 'https://www.google.com/' === $I->getCurrentUrl() );

		$I->loginAs( 'wpsnapshots' );

		$this->setSiteVisibilitySettings( $I,
			[
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-redirect-visitor',
			],
			[
				[
					'field' => 'redirect',
					'value' => 'https://www.google.com/',
					'type'  => 'input',
				],
				[
					'field' => 'redirect_path',
					'value' => true,
					'type'  => 'checkbox',
				],
			]
		);

		$I->logout();

		$I->moveTo( '/some-post/' );

		usleep( 500 );

		$this->assertTrue( 'https://www.google.com/some-post/' === $I->getCurrentUrl() );
	}

	/**
	 * Test restricted access, show a message option
	 */
	public function testRestrictMessage() {
		$I = $this->openBrowserPage();

		$I->loginAs( 'wpsnapshots' );

		$this->networkActivate( $I );

		$this->setMultiSiteVisibilitySettings( $I,
			[
				'mode'       => 'rsa-mode-default',
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$this->setSiteVisibilitySettings( $I,
			[
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-display-message',
			]
		);

		$I->logout();

		$I->moveTo( '/' );

		usleep( 500 );

		$I->seeText( 'Access to this site is restricted' );
	}

	/**
	 * Test restricted access, show a page option
	 */
	public function testRestrictPage() {
		$I = $this->openBrowserPage();

		$I->loginAs( 'wpsnapshots' );

		$this->networkActivate( $I );

		$this->setMultiSiteVisibilitySettings( $I,
			[
				'mode'       => 'rsa-mode-default',
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$this->setSiteVisibilitySettings( $I,
			[
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-unblocked-page',
			],
			[
				[
					'field' => 'rsa_page',
					'value' => '2',
					'type'  => 'select',
				],
			]
		);

		$I->logout();

		$I->moveTo( '/' );

		usleep( 500 );

		$contains = false;

		if ( false !== strpos( $I->getCurrentUrl(), 'sample-page' ) ) {
			$contains = true;
		}

		$this->assertTrue( $contains );
	}

	/**
	 * Test restricted access with an unrestricted IP address
	 */
	public function testRestrictIpAddress() {
		$I = $this->openBrowserPage();

		$I->loginAs( 'wpsnapshots' );

		$this->networkActivate( $I );

		$this->setMultiSiteVisibilitySettings( $I,
			[
				'mode'       => 'rsa-mode-default',
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$this->setSiteVisibilitySettings( $I,
			[
				'visibility' => 'blog-restricted',
				'restricted' => 'rsa-send-to-login',
			]
		);

		$I->click( '#rsa_myip' );

		usleep( 100 );

		$I->click( '#addip' );

		usleep( 100 );

		$I->click( '#submit' );
		$I->waitUntilElementVisible( '#wpadminbar' );

		$I->logout();

		$I->moveTo( '/sample-page/' );

		usleep( 500 );

		$contains = false;

		if ( false !== strpos( $I->getCurrentUrl(), 'sample-page' ) ) {
			$contains = true;
		}

		$this->assertTrue( $contains );
	}

}
