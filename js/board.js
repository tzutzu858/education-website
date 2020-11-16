
function escape(toOutput) {
    return toOutput.replace(/\&/g, '&amp;')
        .replace(/\</g, '&lt;')
        .replace(/\>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/\'/g, '&#x27')
        .replace(/\//g, '&#x2F');
}

function appendCommentToDOM(container, comment, isPrepend) {
    const html = `
    <div class="card">
        <div class="card-body question_block">
            <div class="d-flex justify-content-between">
            <div class="d-flex">
                <div class="card_avartar text-center text-white">
                    ${comment.id}
                </div>
                <h5 class="card-title">${comment.nickname}</h5>
            </div>
                <p>${comment.created_at}</p>
            </div>
            <p class="card-text">${comment.content}</p>
            
                <div class="card card-body reply">
                <p>reply</p>
                <p class="reply_msg">老師太忙尚未回覆，急切問題可直接找老師。</p>   
                </div>
            
        </div>
    </div>
        `
    if (isPrepend) {
        container.prepend(html)
    } else {
        container.append(html)
    }
}

function getCommentsAPI(siteKey, before, cb) {
    let url = `https://tzutzu858.tw/json/api_comments.php?site_key=${siteKey}`
    if (before) {
        url += '&before=' + before
    }
    $.ajax({
        url,
    }).done(function (data) {
        cb(data)
    });
}

function getComments() {
    $('.load-more').hide();
    getCommentsAPI(siteKey, lastId, data => {
        const commentsDOM = $('.comments')
        if (!data.ok) {
            alert(data.message)
            return
        }

        const comments = data.discussions;
        for (let comment of comments) {
            appendCommentToDOM(commentsDOM, comment)
        }
        let length = comments.length
        if (length < 5) {
            $('.load-more').hide();
            return
        } else {
            lastId = comments[length - 1].id
            commentsDOM.append(loadMoreButtonHTML)
        }
    })
}

const siteKey = 'vic'
const loadMoreButtonHTML = '<button class="load-more btn btn-primary">載入更多</button>'
let lastId = null

$(document).ready(() => {
    const commentsDOM = $('.comments')

    //第一次載入資料
    getComments();

    // 載入更多
    $('.comments').on('click', '.load-more', () => {
        getComments();
    })

    // 新增留言
    $('.add-comment-form').submit(e => {
        e.preventDefault();
        const newCommentData = {
            site_key: 'vic',
            nickname: $('input[name=nickname]').val(),
            content: $('textarea[name=content]').val()
        }

        $.ajax({
            type: 'POST',
            url: 'https://tzutzu858.tw/json/api_add_comments.php',
            data: newCommentData
        }).done(function (data) {
            if (!data.ok) {
                alert(data.message)
                return
            }
            $('input[name=nickname]').val('') // 成功之後傳個空字串清空
            $('textarea[name=content]').val('')
            const comments = data.discussions;
            for (let comment of comments) {
                appendCommentToDOM(commentsDOM, comment, true)
            }
        });
    })
})


