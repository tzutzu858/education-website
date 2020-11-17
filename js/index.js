
function changeScore(rowid, quantityInput) {
    quantity = quantityInput.value;
    var percent = 'percent_' + rowid;
    var subtotal_id = 'subtotal_' + rowid;
    var percentage = document.getElementById(percent).value;
    percentage = parseInt(percentage, 10) / 100;
    document.getElementById(subtotal_id).innerText = (quantity * percentage).toFixed(1);
    var sum = 0;
    for (i = 1; i <= 10; i++) {
        const value = $(`#subtotal_${i}`).html();
        sum = sum + parseFloat(value);
        $('.final_num').html(sum.toFixed(1));
    }

}

//登入或登出狀態
var data = sessionStorage.getItem('username');
if (data) {
    $('.sign_in').hide();
    for (i = 1; i <= 10; i++) {
        $(`#percent_${i}`).removeAttr("readOnly");
    }
} else {
    $('.sign_out').hide();
}



$(document).ready(() => {
    $.getJSON('https://tzutzu858.tw/json/api_calculation.php', function(data) {
        if (!data.ok) {
            alert(data.message)
            return
        }
        const calculations = data.calculation;
        let id = 1
        for (let calculation of calculations) {
            $('.score_block').append(`
            <div class="form-group">
                <div class="d-flex flex-row">
                    <span class="badge badge-pill badge-info">${id}</span>
                    <textarea class="title" readonly="readonly">
                    ${calculation.title}
                    </textarea>
                </div>
    
                <div class="d-flex flex-row score_card">
                <div class="col-6">
                 <input type="number" class="form-control" placeholder="Enter the score" onblur="changeScore(${id},this)">
                </div>
                <div class="col d-flex flex-row align-items-center">
                    <span class="txt">占分比例 :</span>
                    <input class="form-control num_block percentage " id="percent_${id}" readonly="readonly" value="${calculation.percent}">
                </div>
                <div class="col d-flex flex-row align-items-center">
                    <span class="txt">百分比成績換算 :</span>
                    <div class="form-control num_block" id="subtotal_${id}" readonly="readonly"></div>
                </div>
                </div>
            </div>
            `)
            id++;
        }

    });



 

    

    

    









    $('#score_form').submit(e => {
        e.preventDefault();
        var sum = 0;
        for (i = 1; i <= 10; i++) {
            const value = $(`#subtotal_${i}`).html();
            sum = sum + parseFloat(value);
        }
        $('.final_num').html(sum.toFixed(1));
    })

    $('.sign_out').click(()=>{
        sessionStorage.removeItem('username');
        window.history.go(0); //0 是重新載入現在頁面
    });
})


