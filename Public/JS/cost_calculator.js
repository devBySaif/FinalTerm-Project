function calculateCost() {
    var baseCost = parseFloat(document.getElementById("baseCost").innerText);
    var travelers = parseInt(document.getElementById("travelers").value);
    var days = parseInt(document.getElementById("days").value);
    
    if(isNaN(travelers) || travelers < 1 || travelers > 10) {
        alert("Number of travelers must be between 1 and 10!");
        document.getElementById("travelers").value = 1;
        return;
    }
    
    if(isNaN(days) || days < 1) {
        alert("Number of days must be at least 1!");
        document.getElementById("days").value = 1;
        return;
    }
    
    var total = baseCost * travelers * (days / 7);
    total = Math.round(total * 100) / 100;
    
    document.getElementById("totalCost").innerText = total.toFixed(2);
}

window.onload = function() {
    calculateCost();
};