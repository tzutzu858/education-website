# education-website
網址 : https://tzutzu858.tw/english_vic
<br>
<br>
![](https://i.imgur.com/R8vJHSZ.png)<br>
<br>
幫朋友做的小網站，最先只是要讓他的學生計算一下成績，所以目前沒有什麼特別的想法，現在就慢慢做慢慢增加功能，不太確定未來要不要做成 app (因為實用性不太高還要特地下載 app 似乎有點麻煩)。<br>
*******

### 預計功能
1. 登入後可以增加/刪除/跳上一句/跳下一句/每日一句英文(完成)
2. 登入後更改計算成績的比例(完成)
3. 登入後回覆及刪除英文問題的留言
4. 修正單字翻譯功能
5. 學生可以查到自己的所有小考成績(但這樣就要做學生登入功能，不然看別人成績看得很開心)

### API

* 英文問題留言版的 [API](https://tzutzu858.tw/json/api_comments.php) : [說明](https://github.com/tzutzu858/API-Server-practice-SPA/blob/main/README.md)<br>
* 登入功能 https://tzutzu858.tw/json/api_login.php  : [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_login.php)<br>
POST 帶上 username、password。<br>
回傳<br>
```
{
    "ok": true,
    "username": "aa"
}
```
***********
#### 每日英文

* 列出一句英文 https://tzutzu858.tw/json/api_list_daily_englich.php : [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_list_daily_englich.php)<br>
資料庫多加一張資料表來記錄此刻 id ， 拿到 id 再去英文句子資料表拿 id 、zh 、en ，要再拿 id 是因為前端要記錄，因為前端其他按鈕會更改到 id <br> 
GET 拿資料<br>
回傳<br>
```
{
    "ok": true,
    "dailyEnglish": [
        {
            "id": 3,
            "zh": "浪費時間就是掠奪自己。",
            "en": "Wasting time is robbing oneself."
        }
    ]
}
```

* 上一句下一句更換英文句子，https://tzutzu858.tw/json/api_update_daily_englich.php : [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_update_daily_englich.php)<br>
主要是更改紀錄英文句子 id 的那張資料表，所以先拿到紀錄英文句子那張資料表拿到 id 再做更新 ，還要拿英文句子資料表的最後一句 id ，sql 句是 ` SELECT * FROM table ORDER BY id DESC LIMIT 0 , 1 ` ，這樣就可以拿到最後一句的 id ，要拿的原因是如果更改過後的 id 比最後一句的 id 還大那就直接 `die();` ， 並且用 do while 做檢查 id 是否有效，用 `num_rows` 來做判斷 ，不是 0 才跳出迴圈，這樣就確保更改過後的 id 是有效的，此時才真正去更新紀錄英文句子 id 的那張資料表<br>
POST 帶上 username、unm。<br>
unm 是讓前端加 1 或減 1 來表示上一句或下一句<br>
回傳<br>
```
{
    "ok": true,
    "dailyEnglishID": [
        {
            "id": 2
        }
    ]
}
```
* 新增英文句子 https://tzutzu858.tw/json/api_add_english_sentence.php  :  [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_add_english_sentence.php)<br>
更新過後用 `@@IDENTITY` ，重新拿剛剛更新後的句子 `SELECT * FROM table WHERE id = @@IDENTITY`<br>
POST 帶上 username、zh、en。<br>
回傳<br>
```
{
    "ok": true,
    "dailyEnglish": [
        {
            "id": 9,
            "zh": "蘋果",
            "en": "apple"
        }
    ]
}
```

* 刪除英文句子 https://tzutzu858.tw/json/api_delete_english_sentence.php  :  [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_delete_english_sentence.php)<br>
使用軟刪除，所以實際是 UPDATE ，`UPDATE table SET is_deleted=1 WHERE username = ? AND id =?`，UPDATE is_deleted=1 後再更改紀錄 id 那張資料表，預計更新成被刪除的下一句，但要判斷下一句的 id 是否也是 `is_deleted=1` ， 因此也是用 do while 做檢查，一直到如果檢查已經是最後一筆的 id ，便迴圈改成檢查更新被刪除的上一句，這樣來確認 id 是否有效沒有被刪除。<br>
POST 帶上 username、id。<br>
回傳的 id 是檢查後沒有被刪除的 id<br>
```
{
    "ok": true,
    "dailyEnglishID": [
        {
            "id": 3
        }
    ]
}
```


![](https://github.com/tzutzu858/challenge-daily-UI/blob/master/gif/sentence.gif?raw=true)<br>

#### 成績計算
* 列出所有成績項目及比例 https://tzutzu858.tw/json/api_calculation.php  :  [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_calculation.php)<br>
GET 拿全部資料<br>
回傳<br>
```
{
    "ok": true,
    "calculation": [
        {
            "id": 1,
            "title": "L1~L9 Worksheets, Assignment & Tests on Google Classroom （上課講義、作業、測驗、每一課各占3%，九課共27%)",
            "percent": 27
        },
        {
            "id": 3,
            "title": "1st Midterm 第一次期中考",
            "percent": 20
        },
        {
            "id": 4,
            "title": "2nd Midterm 第二次期中考",
            "percent": 20
        },
  
  
    ]
}
```

* 更新成績項目及比例 https://tzutzu858.tw/json/api_update_calculation.php :  [程式碼](https://github.com/tzutzu858/education-website/blob/main/api/api_update_calculation.php)<br>
POST 帶上 username、title、percent。<br>
沒有回傳東西<br>


### 卡卡日誌 : 
* 串 Google Translation API 尚未成功，因為想要用 client libraries 的方式但又不太懂，目前先用 [Cambridge Dictionary 的免費搜尋框](https://dictionary.cambridge.org/zht/freesearch.html) 擋著，不想用 AJAX 串 API 是因為這樣那份有放金鑰的 JavaScript 就不能擺來這了，所以先做其他部分， client libraries 再慢慢研究。
