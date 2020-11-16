function changeScore(rowid, quantityInput) {
    quantity = quantityInput.value;
    var percent = 'percent_' + rowid;
    var subtotal_id = 'subtotal_' + rowid;
    var percentage = document.getElementById(percent).innerText;
    percentage = parseInt(percentage, 10) / 100;
    document.getElementById(subtotal_id).innerText = (quantity * percentage).toFixed(1);
    var sum = 0;
    for (i = 1; i <= 10; i++) {
        const value = $(`#subtotal_${i}`).html();
        sum = sum + parseFloat(value);
        $('.final_num').html(sum.toFixed(1));
    }
    
}

$(document).ready(() => {
    $('#score_form').submit(e => {
        e.preventDefault();
        var sum = 0;
        for (i = 1; i <= 10; i++) {
            const value = $(`#subtotal_${i}`).html();
            sum = sum + parseFloat(value);
        }
        $('.final_num').html(sum.toFixed(1));
    })
})


