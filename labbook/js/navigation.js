/**
 * File navigation.js.
 *
 * Handles toggling the navigation menus for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	function setUpMenu( elementId ) {
		/**
		 * Sets or removes .focus class on an element.
		 */
		function toggleFocus() {
			var self = this;

			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( -1 === self.className.indexOf( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					if ( -1 !== self.className.indexOf( 'focus' ) ) {
						self.className = self.className.replace( ' focus', '' );
					} else {
						self.className += ' focus';
					}
				}

				self = self.parentElement;
			}
		}

		/**
		 * Toggles `focus` class to allow submenu access on tablets.
		 */
		function toggleSubmenuFocus( container ) {
			var parentLinks = Array.from( container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' ) );

			if ( 'ontouchstart' in window ) {
				var touchStartFn = function( e ) {
					var menuItem = this.parentNode, i;

					if ( ! menuItem.classList.contains( 'focus' ) ) {
						e.preventDefault();
						for ( i = 0; i < menuItem.parentNode.children.length; ++i ) {
							if ( menuItem === menuItem.parentNode.children[i] ) {
								continue;
							}
							menuItem.parentNode.children[i].classList.remove( 'focus' );
						}
						menuItem.classList.add( 'focus' );
					} else {
						menuItem.classList.remove( 'focus' );
					}
				};

				parentLinks.forEach(
					( parentLink ) => {
						parentLink.addEventListener( 'touchstart', touchStartFn, false );
					}
				);
			}
		}

		var container = document.getElementById( elementId );

		if ( ! container ) {
			return;
		}

		var button = container.getElementsByTagName( 'button' )[0];
		if ( 'undefined' === typeof button ) {
			return;
		}

		var menu = container.getElementsByTagName( 'ul' )[0];

		// Hide menu toggle button if menu is empty and return early.
		if ( 'undefined' === typeof menu ) {
			button.style.display = 'none';
			return;
		}

		menu.setAttribute( 'aria-expanded', 'false' );
		if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
			menu.className += ' nav-menu';
		}

		button.onclick = function() {
			if ( -1 !== container.className.indexOf( 'toggled' ) ) {
				container.className = container.className.replace( ' toggled', '' );
				button.setAttribute( 'aria-expanded', 'false' );
				menu.setAttribute( 'aria-expanded', 'false' );
			} else {
				container.className += ' toggled';
				button.setAttribute( 'aria-expanded', 'true' );
				menu.setAttribute( 'aria-expanded', 'true' );
			}
		};

		// Get all the link elements within the menu.
		var links = Array.from( menu.getElementsByTagName( 'a' ) );

		// Each time a menu link is focused or blurred, toggle focus.
		links.forEach(
			( link ) => {
				link.addEventListener( 'focus', toggleFocus, true );
				link.addEventListener( 'blur', toggleFocus, true );
			}
		);

		toggleSubmenuFocus( container );
	}

	setUpMenu('network-navigation');
	setUpMenu('site-navigation');
} )();
