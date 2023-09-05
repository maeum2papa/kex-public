<script>
    export let handlePopupClose;
    export let handleCloseToday;
    
</script>

<div>
    <div class="popup">
        <div class="contents">
            <h3>◈ 발송 시 라벨 부착 주의 요청</h3> <br/>
            화물 겉면에는 편의점에서 출력한 운송장만 부착합니다. <br/>
            스탁엑스가 제공한 운송장 라벨은 모두 박스 안에 넣어주세요.<br/>
            (바코드 혼재 시, 스캔 오류 발생 가능)<br/>
            <img src="/images/waring_label.png"/>
        </div>
        <div class="button" on:click={handlePopupClose}></div>
        <div class="close-today" on:click={handleCloseToday}>
            오늘 하루 보지 않기
        </div>
    </div>
    <div class="mask" on:click={handlePopupClose}></div>
</div>


<style lang="scss">
    .popup{
        position: fixed;
        z-index: 100;
        top:50%;
        left:50%;
        transform: translate(-50%,-50%);
        background-color: #fff;
        width: 558px;
        height: auto;
        box-shadow: 10px 0px 30px rgba(1,1,1,0.5);
        overflow: hidden;
        h3 { font-size: var(--font-size-normal); }
        .contents {
            font-size: var(--font-size-semi-small);
            line-height: 1.32em;
            padding: 30px 20px;
        }
        .button{
            background:transparent;
            position: absolute;
            top:20px;
            right:20px;
            border:0;
            width:40px;
            height:40px;
            cursor: pointer;

            &::before{
                content: '';
                position: absolute;
                top:50%;
                left: 0;
                width:100%;
                height:1px;
                background: black;
                transform: rotate(45deg);
            }

            &::after{
                content: '';
                position: absolute;
                top:50%;
                left: 0;
                width:100%;
                height:1px;
                background: black;
                transform: rotate(-45deg);
            }
        }
        p { font-size: 0; }
        img {
            width: 100%;
            margin-top: 20px;
        }
        .close-today {
            width: calc(100% - 20px);
            height: 20px;
            font-size: 14px;
            background-color: #000;
            color: #fff;
            line-height: 20px;
            padding:3px 10px;
            text-align: left;
            position: relative;
            cursor: pointer;

            // &::before{
            //     content: "";
            //     display: inline-block;
            //     width:14px;
            //     height:14px;
            //     border:1px solid #fff;
            //     position: absolute;
            //     right:5px;
            //     top:4px;
            // }
        }
    }
    .mask {
        position: fixed;
        z-index: 50;
        width: 100%;
        height: 100%;
    }

@media only screen and (max-width:768px) {

    .popup{
        max-width: 100%;
        height: auto;
        
        button{
            background:transparent;
            position: absolute;
            top:20px;
            right:20px;
            border:0;
            width:20px;
            height:20px;
            
        }
    }

}

    // @media only screen and (max-width:1024px){
        
    // }
    // @media only screen and (max-width:425px){
        
    // }
</style>