<script>
    import Header from "../../../components/Header.svelte";
	import { cAlert } from "../../../stores/store";
	import { postApi } from "../../../services/api";
    import { onMount } from "svelte";
	import { auth } from "../../../services/auth";

	let formData = {
		SFNumber:'',
		mobileLast:''
	}

	let inputs = {
		SFNumber:{},	
		mobileLast:{}
	}

	let orderData = {
		SFNumber:'',
		sender:'',
		postNumber:'',
		address:''
	}

	const handleSfNumber = ()=>{

		const regex = /[^0-9SFsf]/g;
        formData.SFNumber = formData.SFNumber.replace(regex, '');

		if(formData.SFNumber.includes("sf")){
			formData.SFNumber = formData.SFNumber.replace("sf", '');
		}

		if(formData.SFNumber.includes("SF")==false && formData.SFNumber.length>0){
			formData.SFNumber = "SF"+formData.SFNumber;
		}
	}

	const handleSearch = async ()=>{

		if(formData.SFNumber==''){
			$cAlert = {flag:true,msg:'SF 운송장 번호를 입력해 주세요.',feedback:()=>{inputs.SFNumber.focus()}}
            return false;
		}

		if(formData.mobileLast==''){
			$cAlert = {flag:true,msg:'전화번호 뒤 4자리를 입력해 주세요.',feedback:()=>{inputs.mobileLast.focus()}}
            return false;
		}

		const res = await postApi(
			{
				path:"/api/search",
				data:formData
			}
		)

		if(res.msg=='ok'){
			if(res.data.msg=='no'){
				$cAlert = {flag:true,msg:'Bulk 오더의 경우 편의점 발송이 제한됩니다.',feedback:()=>{inputs.SFNumber.focus()}}
				return false;
			}else if(res.data.msg=='no2'){
				$cAlert = {flag:true,msg:'Bulk 오더의 경우 편의점 발송이 제한됩니다.(1)',feedback:()=>{inputs.SFNumber.focus()}}
				return false;
			}else if(res.data.msg=='no3'){
				$cAlert = {flag:true,msg:'접수된 SF 운송장 번호 입니다.',feedback:()=>{inputs.SFNumber.focus()}}
				return false;
			}else{
				orderData = res.data;
			}
		}else{
			orderData = {
				SFNumber:'',
				sender:'',
				postNumber:'',
				address:''
			}
			$cAlert = {flag:true,msg:'데이터를 찾지 못했습니다.',feedback:()=>{inputs.SFNumber.focus()}}
		}
	

		if(orderData.SFNumber!=''){
			setTimeout(()=>{
				window.scrollTo({
					top: document.querySelector('.result-area').offsetTop,
					behavior: "smooth",
				});

			},100)
		}

	}

	const handleCartAdd = async()=>{

		const res2 = await auth();

		orderData.mobileLast = res2.mobile.slice(-4);
		
		const res = await postApi(
			{
				path:'/api/cart/add',
				data:orderData
			}
		)

		if(res.msg=='ok'){
			location.replace('/accept/cart');
		}
	}


	onMount(async()=>{
		const res = await auth();

		if(res.msg=='ok'){
			formData.mobileLast = res.mobile.slice(-4);
			orderData.mobileLast = res.mobile.slice(-4);
			inputs.SFNumber.focus();
		}else{
			location.replace("/");
		}
	});

</script>

<Header/>

<div class="inner-wrap">

	<h1>SF운송장 찾기</h1>

	<section class="search-area">
		<div class="table type2">
			<div class="help"><span class='star'>*</span>  는 필수입력입니다.</div>
			<div class="table-body">
				<div>
					<div>SF 운송장 번호<span class="star">*</span></div>
					<div>
						<input type="text" placeholder="13자리 숫자 입력" bind:value={formData.SFNumber} bind:this={inputs.SFNumber} maxlength={15} on:keyup={handleSfNumber}>
					</div>
				</div>
				<div>
					<div>전화번호 뒤 4자리<span class="star">*</span></div>
					<div>
						<input type="text" placeholder="0000" bind:value={formData.mobileLast} bind:this={inputs.mobileLast} maxlength={4}>
					</div>
				</div>
			</div>
		</div>

		<div class="button-area">
			<button type="button" on:click={handleSearch}>찾기</button>
		</div>

	</section>


	{#if orderData.SFNumber!=''}
	<section class="result-area">

		<div class="table">
			<h2>SF 운송장 발송자 오더 정보</h2>
			<div class="table-body">
				<div>
					<div>운송장 번호</div>
					<div>{orderData.SFNumber}</div>
				</div>
				<div>
					<div>발송자</div>
					<div>{orderData.sender}</div>
				</div>
				<div>
					<div>우편번호</div>
					<div>{orderData.postNumber}</div>
				</div>
				<div>
					<div>발송주소</div>
					<div>{orderData.address}</div>
				</div>
			</div>
		</div>


		<div class="button-area">
			<button type="button" on:click={handleCartAdd}>담기</button>
		</div>

	</section>
	{/if}

</div>


<style lang="scss">
	.inner-wrap{
		margin-top:95px;
		margin-bottom:148px;
	}

	h1{
		font-size:var(--font-size-big-big);
		text-align: center;
	}

	.button-area{
		text-align: center;
		margin-top:67px;
	}

	.result-area{
		margin-top:106px;
	}


	@media only screen and (max-width:425px){
		.inner-wrap{
			margin-top:53px;
			margin-bottom:122px;
		}

		h1{
			font-weight: 500;
		}

		.search-area{
			margin-top:44px;
		}

		.button-area{
			text-align: center;
			margin-top:45px;
		}
	}
</style>