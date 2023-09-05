<script>
    import { cAlert,Loading } from "../../stores/store";
    import { onMount } from "svelte";
    import { postApi } from "../../services/api";
    import { won } from "../../services/format";
    import { auth } from "../../services/auth";
    import Header from "../../components/Header.svelte";  
    import Scan from "../../components/Scan.svelte";
    import Exchange from "../../components/Exchange.svelte";
    import Xls from "../../components/Xls.svelte";
    import Manyscan from "../../components/Manyscan.svelte";

    const statuss = [
        {name : '예약 접수 완료'},
        {name : '운송장 출력 완료'},
        // {name : '예약 확정(집하요청) 완료'},
        {name : '택배 접수 완료'},
        {name : '배송매니저 수거 완료 (집하완료)'},
        {name : '도착점포 입고 완료 (점포 도착)'},
        {name : '배송완료 (고객수취 완료)'},
        // {name : '예약 취소'},
        // {name : '반송'},
    ]


    const kpiStatuss = [
        {name : '집하지연'}, //receipted 발생 이후 영업일 기준 24시간 이상
        {name : '배송지연'}, //gathered 발생 이후 영업일 기준 48시간 이상 지연 시
    ]


    const searchFormData = {
        startResDate:null,
        endResDate:null,
        startRegDate:null,
        endRegDate:null,
        sender:'',
        status:'',
        kpiStatus:'',
        sfNumber:'',
        convSfNumber:[],
        resNumber:'',
        convResNumber:[],
        deliveryNumber:'',
        convDeliveryNumber:[],
        startColDate: null,
        endColDate: null,
        startDelDate: null,
        endDelDate: null,
    }
    

    let exchangePopFalg = false;
    let autoPrintFlag = false;
    let manyPrintFlag = false;
    let xlsFlag = false;


    const handleMask = () => {
        exchangePopFalg = false;
        autoPrintFlag = false;
        xlsFlag = false;
        manyPrintFlag = false;
    }

    const handleExchange = () => {
        exchangePopFalg = true;
    }

    const handlePrint = ()=> {
        autoPrintFlag = true;
    }

    const handleXls = () =>{
        if(list.length==0){
            $cAlert = {flag:true,msg:'엑셀 출력에 필요한 데이터가 없습니다.',feedback:()=>{}};
            return false;
        }
        xlsFlag = true;
    }

    const handleManyPrint = () =>{
        if(list.length==0){
            $cAlert = {flag:true,msg:'엑셀 출력에 필요한 데이터가 없습니다.',feedback:()=>{}};
            return false;
        }
        manyPrintFlag = true;
    }
    

    const handleSearch = async() => {

        await auth();

        converter();
        
        $Loading = {flag:true};

        let searchAllowed = false;


        if(searchFormData.startResDate!=null || searchFormData.endResDate!=null || searchFormData.startRegDate!=null || searchFormData.endRegDate!=null || searchFormData.startColDate!=null || searchFormData.endColDate!=null || searchFormData.startDelDate!=null || searchFormData.endDelDate!=null){
            searchAllowed = true;
        }

        if(searchFormData.sender!='' || searchFormData.status!='' || searchFormData.kpiStatus!='' || searchFormData.sfNumber!='' || searchFormData.resNumber!='' || searchFormData.deliveryNumber!=''){
            searchAllowed = true;
        }

        if(!searchAllowed){
            $cAlert = {flag:true,msg:"검색조건을 입력해 주세요. 너무 많은 데이터 검색은 서버 과부하의 원인이 됩니다.",feedback:()=>{$Loading = {flag:false}}};
            return false;
        }

        
        const res  = await postApi({
            path:"/api/list",
            data:searchFormData
        });
        if(res.msg=='ok'){
            list = res.list;
        }

        $Loading = {flag:false};
    }

    let list = [];



    function converter(){

        searchFormData.convSfNumber = searchFormData.sfNumber.split("\n");
        searchFormData.convResNumber = searchFormData.resNumber.split("\n");
        searchFormData.convDeliveryNumber = searchFormData.deliveryNumber.split("\n");

    }


    onMount(async()=>{

        const user = await auth();

        if(user==''){
            location.replace("/login");
        }

        /*
        const res = await postApi({
            path:'/api/auth'
        });

        if(res.msg!='ok'){
            location.replace("/login");
        }
        */

    });

</script>

<Header/>

<div class="wrap">
    

    <section>
        <table>
            <tr>
                <td>
                    <div class="table">
                        <div class='th'>예약일자</div>
                        <div><input type="date" bind:value={searchFormData.startResDate}> ~ <input type="date" bind:value={searchFormData.endResDate}></div>
                    </div>
                </td>
                <td>
                    <div class="table">
                        <div class='th'>접수일자</div>
                        <div><input type="date" bind:value={searchFormData.startRegDate}> ~ <input type="date" bind:value={searchFormData.endRegDate}></div>
                    </div>
                </td>
                <td>
                    <div class="table">
                        <div class='th'>집하일자</div>
                        <div><input type="date" bind:value={searchFormData.startColDate}> ~ <input type="date" bind:value={searchFormData.endColDate}></div>
                    </div>
                </td>
                <td>
                    <div class="table">
                        <div class='th'>배송일자</div>
                        <div><input type="date" bind:value={searchFormData.startDelDate}> ~ <input type="date" bind:value={searchFormData.endDelDate}></div>
                    </div>
                </td>
                <td>
                    <div class="table">
                        <div class='th'>발송자명</div>
                        <div><input type="text" bind:value={searchFormData.sender}></div>
                    </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div class="table">
                        <div class='th'>화물상태</div>
                        <div>
                            <select bind:value={searchFormData.status}>
                                <option value="">전체</option>
                                {#each statuss as item}
                                    <option value={item.name}>{item.name}</option>
                                {/each}
                            </select>
                        </div>
                    </div>
                </td>
                <td rowspan={2}>
                    <div class="table row">
                        <div class='th'>SF운송장번호</div>
                        <div><textarea bind:value={searchFormData.sfNumber}></textarea></div>
                    </div>
                </td>
                <td rowspan={2}>
                    <div class="table row">
                        <div class='th'>승인번호</div>
                        <div><textarea bind:value={searchFormData.resNumber}></textarea></div>
                    </div>
                </td>
                <td rowspan={2}>
                    <div class="table row">
                        <div class='th'>택배사운송장</div>
                        <div><textarea bind:value={searchFormData.deliveryNumber}></textarea></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="table">
                        <div class='th'>KPI상태</div>
                        <div>
                            <select bind:value={searchFormData.kpiStatus}>
                                <option value="">전체</option>
                                {#each kpiStatuss as item}
                                    <option value={item.name}>{item.name}</option>
                                {/each}
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </section>

    <section class="button-area">
        <button type="button" on:click={handleSearch}>검색</button>
        <button type="button" class='xlsButton' on:click={handleXls}>엑셀추출</button>
        <button type="button" on:click={handlePrint}>SF운송장출력</button>
        <button type="button" on:click={handleExchange}>고시환율입력</button>
    </section>
    <section class="button-area">
        <button type="button" class='xlsButton type2' on:click={handleManyPrint}>프린터 템플릿 추출</button>
    </section>

    <section class="list-area">

        <table>
            <tr class="list-head">
                <th>S/N</th>
                <th>1. 화물상태</th>
                <th>2. KPI상태</th>
                <th>SF운송장번호</th>
                <th>승인번호</th>
                <th>택배사<br/>운송장번호</th>
                <th>고객오더번호2</th>
                <th>발송자명</th>
                <th>전화번호</th>
                <th>발송자명<br/>(IUOP 오더 상)</th>
                <th>물품종류</th>
                <th>물품가액</th>
                <th>물품가액2</th>
                <th>예약일시</th>
                <th>접수일시</th>
                <th>집하일시</th>
                <th>배송일시</th>
            </tr>
            {#each list as item}
            <tr class="list-body">
                <td>{item.id}</td>
                <td>{item.status}</td>
                <td>{item.kpiStatus}</td>
                <td>{item.sfNumber}</td>
                <td>{item.resNumber}</td>
                <td>{item.deliveryNumber == null ? '' : item.deliveryNumber}</td>
                <td>{item.orderNumber}</td>
                <td>{item.sender}</td>
                <td>{item.mobile}</td>
                <td>{item.iuopSender}</td>
                <td>{item.item}</td>
                <td>{won(item.itemPrice)}</td>
                <td>{won(item.itemPrice2)}</td>
                <td>{item.reservationDate == null ? '' : item.reservationDate}</td>
                <td>{item.registerDate == null ? '' : item.registerDate}</td>
                <td>{item.collectionDate == null ? '' : item.collectionDate}</td>
                <td>{item.deliveryDate == null ? '' : item.deliveryDate}</td>
            </tr>
            {/each}
        </table>

    </section>

</div>

{#if exchangePopFalg}
    <Exchange {handleMask}></Exchange>
    <div class="mask" on:click={handleMask}></div>
{/if}


{#if autoPrintFlag}
    <Scan {handleMask}></Scan>
    <div class="mask" on:click={handleMask}></div>
{/if}

{#if xlsFlag}
    <Xls {handleMask} {list}></Xls>
    <div class="mask" on:click={handleMask}></div>
{/if}

{#if manyPrintFlag}
    <Manyscan {handleMask} {list}></Manyscan>
    <div class="mask" on:click={handleMask}></div>
{/if}


<style lang="scss">
    .table.row{
        .th{
            height:89px;
            line-height: 89px;
        }

        textarea{
            min-height:76px;
        }
    }
    td{
        padding:5px;
        vertical-align: top;
    }

    .button-area{
        margin-top:10px;
        padding-left: 5px;
        button{
            margin-right:10px;
        }
    }

    .list-area{
        margin-top:40px;
        table{
            width:100%;
        }
    }

    .list-head,
    .list-body{
        border-top:2px solid black;
        border-bottom:2px solid black;
        

        th,td{
            vertical-align: middle;
            padding:5px;
            border-left:1px dashed gray;
            text-align: center;

            &:last-of-type{
                border-right:1px dashed gray;
            }
        }

    }

    .list-body{
        border-top:0;
        border-bottom:1px solid gray;
    }


    

    .mask{
        position: fixed;
        top:0px;
        left:0px;
        width:100vw;
        height:100vh;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
    }

    .xlsButton{
        width:160px;

        &.type2{
            margin-left:70px;
        }
    }
</style>