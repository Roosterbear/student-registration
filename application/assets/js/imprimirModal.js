function imprimirModal(){    
    $("#printable").printThis({ 
    debug: false,              
    printContainer: true,       
    pageTitle: "Ficha",             
    removeInline: false,        
    printDelay: 333,            
    header: null, 
    formValues: true          
    }); 
}