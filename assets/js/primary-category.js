jQuery(function($){

    // When a Category is changed in the Categories box
    $( '#categorychecklist li input:checkbox' ).on( 'change', function() {

        var categoryIdChanged = $( this ).val();
        var categorySelectedParent = $( this ).parent();
        var categorySelectedLabel = categorySelectedParent.text();

        var primaryCategorySelect = $( '#dld_primary_category_select' );
        var primaryCategorySelectOptions = primaryCategorySelect.find( 'option' );

        var isInPrimaryCategorySelect = false;
        var primaryCategoryExistingId;

        // Go through all the already selected categories in the Primary Category Select Box
        $.map( primaryCategorySelectOptions, function( option, i ) {

            // If the Select option already exists
            // make isInPrimaryCategory true and get the value / ID
            if( categoryIdChanged == option.value ) {

                isInPrimaryCategorySelect = true;
                primaryCategoryExistingId = option.value;
            }
        });

        // This option is in the select box already, remove it
        // Otherwise it is not and we can add it as a choice
        if( isInPrimaryCategorySelect && !$( this ).checked ) {

            primaryCategorySelect.find( 'option[value="' + primaryCategoryExistingId + '"]' ).remove();
        } else {

            primaryCategorySelect.append( '<option value="' + categoryIdChanged + '">' + categorySelectedLabel + '</option>' );
        }
    });


});