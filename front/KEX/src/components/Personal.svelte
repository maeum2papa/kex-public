<script>
    export let handleMask;
</script>

<section>
    <div class="close" on:click={handleMask}></div>
    <h3>개인정보 수집 및 이용</h3>
    <p>KEX EXPRESS는 이용자들의 원활한 서비스 제공을 위해 필요한 최소한의 개인정보를 수집하고 있으며,수집 목적 이외에 용도로는 사용되지 않습니다.
 
■ 택배 예약
1. 수집 항목
- 송화인(개인)/수화인(개인) 성명, 휴대전화번호

2. 수집 목적
- 택배 예약 서비스 제공 목적

3. 보유기간
- 3개월</p>
</section>

<style lang="scss">

    section{
        position: fixed;
        max-width: calc(1120px - 80px);
        height: calc(100vh - 360px);
        background: #fff;
        z-index: 2100;
        top:50%;
        left:50%;
        transform: translate(-50%,-50%);
        
        padding:40px;
    }

    h3{
      font-size:var(--font-size-normal);
      padding-bottom: 20px;
      border-bottom:1px solid var(--border-color-gray);
    }

    p{
        word-break: keep-all;
        white-space:pre-wrap;
        overflow-y: scroll;
        height: calc(100vh - 430px);
        margin-top:var(--font-size-small-small-small);
        line-height: 2em;
        color:var(--font-light-gray);
    }

    .close{
        position: absolute;
        width:30px;
        height:30px;
        right:40px;
        top:30px;
        cursor: pointer;
        &::before,&::after{
            content: '';
            position: absolute;
            width:100%;
            height:2px;
            background: black;
            top:50%;
            transform: rotate(45deg);
        }
        &::after{
            transform: rotate(-45deg);
        }
    }

    @media only screen and (max-width:1120px){
        section{
            min-width: 0;
            padding:30px;
            width:calc(100% - 100px);
            margin: 0 auto;
        }

        p{
            height: calc(100vh - 410px);
        }

        .close{
            right:30px;
            top:22px;
        }
    }
</style>