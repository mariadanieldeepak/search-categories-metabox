/* global selectedCategories:true */

(function($) {

	// $ Works! You can test it with next line if you like
	// console.log($);

	$( 'document' ).ready(function() {

		// Global declarations

		// Contains selected Category ID to be sent in AJAX request.
		var selectedCategories = getSelectedCategories();

		// Function definitions.

		/**
		 * @summary Sorts the Categories by name and then by checked items.
		 *
		 * @since 0.1.0
		 *
		 * @global type selectedCategories List of selected Categories ID.
		 */
		function sortCategoriesListBySelectedItems() {

			var selectedCategories = [],
				otherCategories    = [],
				checkBoxState;

			$( 'ul#scm-categories-ul li' ).each(function () {
				checkBoxState = $( this ).find( 'input' ).prop( 'checked' );
				if ( true === checkBoxState ) {
					selectedCategories.push( $( this ) );
				} else {
					otherCategories.push( $( this ) )
				}
			});

			$( 'ul#scm-categories-ul' )
				.html( selectedCategories )
				.append( otherCategories );
		}

		/**
		 * @summary Gets the User selected Categories.
		 *
		 * @since 0.1.0
		 *
		 * @return array List of User selected Categories.
		 */
		function getSelectedCategories() {
			// Category ID's.
			var categories = [];
			// Get selected post Category ID's by looping through selected checkboxes.
			$.each( $( 'input.scm-category-list-item[type=checkbox]:checked' ), function() {
				categories.push( $( this ).val() );
			});
			// Return the array of selected Category ID's.
			return categories;
		}

		// Page Load defaults.
		$( 'p#scm-category-add' ).hide();

		// Event definitions.

		// Sort Categories list so that the selected items appear at the top.
		sortCategoriesListBySelectedItems();

		// Capture the user selected Categories after page loads.
		$( '#scm-categories-ul' ).on('click', '.scm-category-list-item', function() {

			var checkBoxState = $( this ).prop( 'checked' );
			var checkBoxVal   = $( this ).val();

			/*
			 * When user deselects, remove the item from
			 * global selectedCategories
			 */
			if ( false === checkBoxState ) {
				if ( selectedCategories.length > 0 && $.inArray( checkBoxVal, selectedCategories ) !== -1 ) {
					selectedCategories = $.grep(selectedCategories, function( a ) {
						return a !== checkBoxVal;
					});
				}
				$( this ).removeAttr( 'checked' );
			} else {
				/*
				 * When user selects, add the item to
				 * global selectedCategories
				 */
				if ( selectedCategories.length === 0 || $.inArray( checkBoxVal, selectedCategories ) === -1 ) {
					selectedCategories.push( checkBoxVal );
				}
				$( this ).attr('checked', 'checked');
			}
		});

		/**
		 * @summary Displays error message in Search Categories Metabox.
		 *
		 * @since 0.1.0
		 *
		 * @global array selectedCategories List of selected Categories ID.
		 */
		function displayError() {
			var errorMessage;

			if ( $.type( ajax_object.something_wrong_error_message ) != 'undefined' ) {
				errorMessage = ajax_object.something_wrong_error_message;
			} else {
				errorMessage = 'Something went wrong.';
			}

			$( 'ul#scm-categories-ul' ).html( '<li>' + errorMessage  + '</li>' );
			// Reset Selected Categories.
			selectedCategories = [];
		}

		/*
		 * When user types on the search box,
		 * retrieve categories based on the search term.
		 */
		$( '#scm-search-categories-search' ).keyup(function() {

			var searchQuery = $( this ).val();

			$.ajax({
				method: "POST",
				url: ajax_object.ajax_url,
				data: {
					action    : "search_categories",
					categories: selectedCategories,
					query     : searchQuery,
					security  : ajax_object.ajax_nonce,
					post_id   : ajax_object.post_id
				}
			}).success(function( response ) {

				var jsonResult = response.data;

				if ( response.success ) {
					if( '' != jsonResult.category_list ) {
						$( 'ul#scm-categories-ul' ).html( jsonResult.category_list );
					} else {
						// Error message to display when no results are found.
						var noResultsFound;

						if ( $.type( ajax_object.no_results_error_message ) != 'undefined' ) {
							noResultsFound = ajax_object.no_results_error_message;
						} else {
							noResultsFound = 'No results found.';
						}

						$( 'ul#scm-categories-ul' ).html( '<li>' + noResultsFound  + '</li>' );
					}
					sortCategoriesListBySelectedItems();
				} else {
					// If AJAX fails to set JSON success, then display error.
					displayError();
				}

			}).error(function() {
				// If AJAX request fails to send response, then display error.
				displayError();
			});

		});

		/*
		 * Clear the search term when the `Clear` button is clicked.
		 * Trigger the keyup function after clearing, to retrieve all applicable Categories.
		 */
		$('#scm-search-clear').click(function(event){
			event.preventDefault();
			$('#scm-search-categories-search').val('');
			$('#scm-search-categories-search').keyup();
		});

		/*
		 * Toggles the visibility of `Add New Category` section.
		 */
		$( '#scm-category-add-toggle' ).on('click', function () {
			$( 'p#scm-category-add' ).toggle();
		});

	});

})( jQuery );