<script>
    export let handleMask;
</script>

<section>
    <div class="close" on:click={handleMask}></div>
    <h3>개인정보 위탁처리 동의</h3>
    <div class="contents">
        <p>KEX EXPRESS는 원활한 서비스 제공을 위하여 일부 업무를 외부 업체에 위탁하고 있으며, 위탁받은 업체가 관련 법령에 따라 개인정보를 안전하게 처리하도록 필요한 사항을 규정하고 관리/감독하고 있습니다.</p>
        <table>
            <tr>
                <th>제공받는 자 (연락처)</th>
                <th>제공받는 자가 소재하는 국가</th>
                <th>제공받는 자의 개인정보 이용목적</th>
                <th>제공하는 개인정보의 항목</th>
                <th>제공받는 자의 개인정보 보유 및 이용기간</th>
            </tr>
            <tr>
                <td>국세청<br/>(126)</td>
                <td>한국</td>
                <td>부가가치세 등 각종 세금의 부과, 감면, 징수</td>
                <td>성명, 전화번호(회사, 집, 휴대전화), 팩스번호(회사, 집), 주소(회사, 집), 은행계좌번호</td>
                <td>개인정보 이용목적을 달성할 때까지</td>
            </tr>
            <tr>
                <td>관세청<br/>(1577-8577)</td>
                <td>한국</td>
                <td>관세 부과 및 징수, 화물 관리</td>
                <td>성명, 전화번호(회사, 집, 휴대전화), 팩스번호(회사, 집), 주소(회사, 집)</td>
                <td>상동</td>
            </tr>
            <tr>
                <td>나이스 평가정보<br/>(02-3771-1600)</td>
                <td>한국</td>
                <td>신용평가 (채무불이행 정보 확인)</td>
                <td>회사명, 사업자등록번호</td>
                <td>상동</td>
            </tr>
            <tr>
                <td>고려신용정보<br/>(1522-3333)</td>
                <td>한국</td>
                <td>신용평가 및 채권추심</td>
                <td>회사명, 사업자등록번호, 전화번호(회사, 집, 휴대폰번호), 주소, 대표자 성명</td>
                <td>상동</td>
            </tr>
            <tr>
                <td>일양익스프레스<br/>경동택배<br/>GS networks</td>
                <td>한국</td>
                <td>화물배송/td>
                <td>성명(한글, 영문), 회사명, 국가, 전화번호(회사, 집, 휴대전화), 팩스번호(회사, 집), 주소(도시, 주/도, 국가 포함), 우편번호, 고객번호, 사업자등록번호, 대표자 성명, 담당자 성명(한글, 영문), 회사주소, 우편번호</td>
                <td>상동</td>
            </tr>
        </table>
        <br/>
        <b>개인정보 위탁</b>
        <table>
            <tr>
                <th>위탁받는 자</th>
                <th>위탁하는 업무의 내용</th>
            </tr>
            <tr>
                <td>대교 관세사무소</td>
                <td>통관업무</td>
            </tr>
            <tr>
                <td>에이원 관세사무소</td>
                <td>통관업무</td>
            </tr>
            <tr>
                <td>유한 테크노스</td>
                <td>통관업무 관련 정보 처리 시스템 제공</td>
            </tr>
        </table>

        <br/>
        <b>개인정보 국외이전</b>
        <table>
            <tr>
                <th>제공받는 자 (연락처)</th>
                <th>제공받는 자가 소재하는 국가</th>
                <th>제공받는 자의 개인정보 이용목적</th>
                <th>제공하는 개인정보의 항목</th>
                <th>제공받는 자의 개인정보 보유 및 이용기간</th>
            </tr>
            <tr>
                <td>에스에프익스프레스 본사, 각국 계열사 및 해외 고객 센터</td>
                <td>중국, 미국, 싱가포르, 유럽, 태국, 베트남, 말레이시아 등</td>
                <td>해외 배송 및고객관리</td>
                <td>성명(한글, 영문), 회사명, 국가, 전화번호(회사, 집, 휴대전화), 팩스번호(회사, 집), 주소(도시, 주/도, 국가 포함), 우편번호, 아이디, 고객번호, 사업자등록번호, 대표자 성명, 담당자 성명(한글, 영문), 회사주소, 우편번호</td>
                <td>개인정보 이용목적을 달성할 때까지</td>
            </tr>
        </table>
        		
    </div>
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

    .contents{
        overflow-y: scroll;
        height: calc(100vh - 430px);
        margin-top:var(--font-size-small-small-small);
        line-height: 2em;
        color:var(--font-light-gray);
        p{
            word-break: keep-all;
            white-space:pre-wrap;
        }
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

    table{
        margin:5px 0px;
        th,td{
            border:1px solid var(--border-color-gray);
            padding:5px;
            vertical-align: middle;
            line-height: 1.32;
            font-size:14px;
        }
    }

    @media only screen and (max-width:1120px){
        section{
            min-width: 0;
            padding:30px;
            width:calc(100% - 100px);
            margin: 0 auto;
        }

        .contents{
            height: calc(100vh - 410px);
        }

        .close{
            right:30px;
            top:22px;
        }
    }
</style>