    // The DOM elements you wish to replace with Tagify
    var input = document.querySelector("#kt_tagify_6");
    var input = document.querySelector("#kt_tagify_7");
    
    // Initialize Tagify script on the above inputs
    new Tagify(input, {
        whitelist: ["Ada", "Adenine", "Agda", "Agilent VEE"],
        maxTags: 10,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: "tagify__inline__suggestions", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    });
    
    new Tagify(input, {
        whitelist: ["Ada", "Adenine", "Agda", "Agilent VEE"],
        maxTags: 10,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    });