<script>
    import * as XLSX from 'xlsx/xlsx.mjs';
    import { saveAs } from 'file-saver';
    import { onMount } from 'svelte';

    export let handleMask;
    export let list;

    let table;

    let columns = [
        "Order creation time",
        "Customer order",
        "SF Waybill number",
        "Sub transport single",
        "Service provider order",
        "Delivery method",
        "Tax payment method",
        "Destination",
        "Consignor company",
        "Consignor name",
        "Consignor state / province",
        "Consignor city",
        "Consignor address",
        "Consignor telephone number",
        "Consignor postal code",
        "Consignee company",
        "Consignee name",
        "Consignee state / province",
        "Consignee city",
        "Consignee address",
        "Consignee telephone number",
        "Consignee E-mail",
        "Consignee postal code",
        "Insurance type",
        "Insurance value",
        "Pieces",
        "Weight",
        "GrossWeight",
        "VolumeWeight",
        "ChargingWeight",
        "Return?",
        "Package types",
        "English declared name1",
        "Chinese declared name1",
        "Declared amount (USD)1",
        "quantity1",
        "HS Code1",
        "English declared name2",
        "Chinese declared name2",
        "Declared amount (USD)2",
        "quantity2",
        "HS Code2",
        "English declared name3",
        "Chinese declared name3",
        "Declared amount (USD)3",
        "quantity3",
        "HS Code3",
        "English declared name4",
        "Chinese declared name4",
        "Declared amount (USD)4",
        "quantity4",
        "HS Code4",
        "English declared name5",
        "Chinese declared name5",
        "Declared amount (USD)5",
        "quantity5",
        "HS Code5",
        "English declared name6",
        "Chinese declared name6",
        "Declared amount (USD)6",
        "quantity6",
        "HS Code6"
    ]

    const handleExport = async()=>{
        exportExcel();
    }


    function s2ab(s) { 
        var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
        var view = new Uint8Array(buf);  //create uint8array as viewer
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
        return buf;    
    }

    function exportExcel(){ 
        // step 1. workbook 생성
        var wb = XLSX.utils.book_new();

        // step 2. 시트 만들기 
        var newWorksheet = XLSX.utils.table_to_sheet(table);

        let trueColumn = columns;

        for(let j=0; j<trueColumn.length; j++){
            if(trueColumn[j].use!=true){
                trueColumn.splice(j,1);
            }
        }
        
        for(let key in newWorksheet){    
            if(!key.includes('!')){
                newWorksheet[key].v = newWorksheet[key].v.substring(1);
            }
        }

        
        // step 3. workbook에 새로만든 워크시트에 이름을 주고 붙인다.  
        XLSX.utils.book_append_sheet(wb, newWorksheet, 'Sheet1');

        // step 4. 엑셀 파일 만들기 
        var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});

        // step 5. 엑셀 파일 내보내기
        let date = new Date;
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'print_template_'+date.getTime()+'.xlsx');
    }
    
    onMount(()=>{
        handleExport();

        setTimeout(()=>{
            handleMask();
        },10)
    });

</script>


<div class="hidden">
    <table bind:this={table}>
        <tr>
            {#each columns as item}
            <th>^{item}</th>
            {/each}
        </tr>
        {#each list as listItem}
        <tr>
            {#each columns as item}
            <td>
                {#if item=='Customer order'}
                    ^{listItem.deliveryNumber == null ? '' :listItem.deliveryNumber}
                {/if}

                {#if item=='SF Waybill number'}
                    ^{listItem.sfNumber == null ? '' :listItem.sfNumber}
                {/if}
            </td>
            {/each}
        </tr>
        {/each}
    </table>
</div>

<style lang="scss">
    .ahidden{
        overflow: hidden;
        width:0;
        height:0;
    }
</style>