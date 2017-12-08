$(function () {
    //http://www.tutorialrepublic.com/twitter-bootstrap-tutorial/bootstrap-typeahead.php
    // Defining the local dataset
    // var cars = ['Audi', 'BMW', 'Bugatti', 'Ferrari', 'Ford', 'Lamborghini', 'Mercedes Benz', 'Porsche', 'Rolls-Royce', 'Volkswagen'];

    // Constructing the suggestion engine
//    var cars = new Bloodhound({
//        datumTokenizer: Bloodhound.tokenizers.whitespace,
//        queryTokenizer: Bloodhound.tokenizers.whitespace,
//        local: cars
//    });
    /*var dataSource = new Bloodhound({
     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('country'),
     queryTokenizer: Bloodhound.tokenizers.whitespace,
     prefetch: {
     url: "http://jsbin.com/nepazu/1.json"
     }
     });
     dataSource.initialize();*/
    // instantiate the bloodhound suggestion engine
    var engine = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace(datum.type);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: "http://localhost:8888/InClassCoffeebuzz_API/product",
            filter: function (data) {
                console.log("data", data.items);
                return $.map(data.items, function (item) {
                    return {
                        type: item.type,
                        category: item.category
                    };
                });
            }
        }
    });

    // initialize the bloodhound suggestion engine
    engine.initialize();

    // Initializing the typeahead
    $('.typeahead').typeahead({
        hint: true,
        highlight: true, /* Enable substring highlighting */
        minLength: 1 /* Specify minimum characters required for showing result */
    },
            {
                //name: 'cars',
                //source: cars
                //displayKey: 'country',
                //source: dataSource.ttAdapter()
                name: 'engine',
                displayKey: 'type',
                source: engine.ttAdapter()
            });

});  