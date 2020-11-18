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

function getCalculationList() {
    $.getJSON('http://localhost/hw1_12/api_calculation.php', function (data) {
        if (!data.ok) {
            alert(data.message)
            return
        }
        const calculations = data.calculation;
        let id = 1
        for (let calculation of calculations) {
            $('.score_block').append(`
            <div class="form-group">
                <input type="hidden" name="id" value="${calculation.id}">
                <div class="d-flex flex-row">
                    <span class="badge badge-pill badge-info">${id}</span>
                    <textarea class="title" id="title_${id}" readonly="readonly">${calculation.title}</textarea>
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
}

function getCalculationValue() {
    $.getJSON('http://localhost/hw1_12/api_calculation.php', function (data) {
        if (!data.ok) {
            alert(data.message)
            return
        }
        const calculations = data.calculation;
        let id = 1
        for (let calculation of calculations) {
            $(`#title_${id}`).val(`${calculation.title}`);
            $(`#percent_${id}`).val(`${calculation.percent}`);
            id++;
        }
    });
}


//登入或登出狀態
var data = sessionStorage.getItem('username');
if (data) {
    $('.sign_in').hide();
    $('.btn_daily_english_block').show();
    $('#btn_calculation_edit').show();
    $('#btn_calculation_save').hide();
    $('#btn_calculation_cancel').hide();

} else {
    $('.sign_out').hide();
    $('.change_calculation_btn').hide();
    $('.btn_daily_english_block').hide();
    $('#btn_calculation_edit').hide();
    $('#btn_calculation_cancel').hide();
}



$(document).ready(() => {

    //第一次列表
    getCalculationList();


    // 計算最後成績
    $('#score_form').submit(e => {
        e.preventDefault();
        var sum = 0;
        for (i = 1; i <= 10; i++) {
            const value = $(`#subtotal_${i}`).html();
            sum = sum + parseFloat(value);
        }
        $('.final_num').html(sum.toFixed(1));
    })

    // 登出
    $('.sign_out').click(() => {
        sessionStorage.removeItem('username');
        $('.sign_in').show();
        $('.sign_out').hide();
        $('.btn_daily_english_block').hide();
        $('#btn_calculation_edit').hide();
        $('#btn_calculation_cancel').hide();
        $('.btn_calculation_save').remove();
        $('.title').attr("readonly", "readonly");
        $('.title').removeAttr("style");
        for (i = 1; i <= 10; i++) {
            $(`#percent_${i}`).attr("readonly", "readonly");
        }
        getCalculationValue();
    });

    // 更新項目
    $('#btn_calculation_edit').click(() => {
        $('#btn_calculation_edit').hide();
        $('#btn_calculation_save').show();
        $('#btn_calculation_cancel').show();
        for (i = 1; i <= 10; i++) {
            $(`#percent_${i}`).removeAttr("readOnly");
        }
        $('.title').removeAttr("readOnly");
        $('.title').css("border", "1px solid  rgba(29, 29, 29, 0.144)");
        $('.title').css("background-color", "white");
        const saveButtonHTML = '<div class="btn btn_change_calculation btn-primary btn_calculation_save" style="margin-top: .6rem;" >Save</div>'
        $('.form-group').append(saveButtonHTML);

    });

    $('#btn_calculation_cancel').click(() => {
        $('#btn_calculation_cancel').hide();
        $('.btn_calculation_save').remove();
        $('#btn_calculation_edit').show();
        $('.title').attr("readonly", "readonly");
        $('.title').removeAttr("style");
        for (i = 1; i <= 10; i++) {
            $(`#percent_${i}`).attr("readonly", "readonly");
        }
        getCalculationValue();
    });

    //事件代理人，監聽地下動態產生的東西
    $('.score_block').on('click', '.btn_calculation_save', (e) => {

        let id = e.target.parentNode.children[0];
        let title = e.target.parentNode.children[1].children[1];
        let percent = e.target.parentNode.children[2].children[1].children[1];

        const newCalculation = {
            id: id.value,
            username: data,
            title: title.value,
            percent: percent.value,
        }

        $.ajax({
            type: 'POST',
            url: 'http://localhost/hw1_12/api_update_calculation.php',
            data: newCalculation
        }).done(function (data) {
            if (!data.ok) {
                alert(data.message)
                return
            }
            const updateCalculation = data.calculation;
            for (let update of updateCalculation) {
                id.value = update.id;
                title.value = update.title;
                percent.value = update.percent;
            }
            $('.toast').toast('show');
        });
    });
})
