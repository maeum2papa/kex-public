<script>
    import Agree from "../../../components/Agree.svelte";
	import Header from "../../../components/Header.svelte";
    import Personal from "../../../components/Personal.svelte";
	import Entrust from "../../../components/Entrust.svelte";
	import Precautions1 from "../../../components/Precautions1.svelte";
	import Precautions2 from "../../../components/Precautions2.svelte";
	import Precautions3 from "../../../components/Precautions3.svelte";
	import { cAlert } from "../../../stores/store";
    
	

	let agree1View = false;
	let agree2View = false;
	let agree3View = false;

	let formData = {
		agree1:false,
		agree2:false,
		agree3:false,
		agree4:false,
		agree5:false,
		agree6:false,
		agree7:false,
		agree8:false,
	}


	const handleAgree = ()=>{

		if(!formData.agree1){
			$cAlert = {flag:true,msg:"이용약관 동의는 필수 입니다.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree2){
			$cAlert = {flag:true,msg:"개인정보 수집 및 이용 동의는 필수 입니다.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree3){
			$cAlert = {flag:true,msg:"개인정보 위탁처리 동의는 필수 입니다.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree4){
			$cAlert = {flag:true,msg:"만 14세 이상만 이용할 수 있습니다.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree5){
			$cAlert = {flag:true,msg:"예약 전 유의사항을 확인해 주세요.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree6){
			$cAlert = {flag:true,msg:"발송 물품 유의사항을 확인해 주세요.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree7){
			$cAlert = {flag:true,msg:"발송 시 라벨 부착 주의사항을 확인해 주세요.",feedback:()=>{}};
			return false;
		}

		if(!formData.agree8){
			$cAlert = {flag:true,msg:"파손면책 동의는 필수 입니다.",feedback:()=>{}};
			return false;
		}

		/*
		formData = {
			agree1:true,
			agree2:true,
			agree3:true,
			agree4:true,
			agree5:true,
			agree6:true,
			agree7:true,
			agree8:true
		}
		*/

		location.href='/login';
	}

	const handleMask = ()=>{
		agree1View = false;
		agree2View = false;
		agree3View = false;
	}

	const handleAgreeView = (e)=>{
		const idx = e.target.getAttribute('idx');

		if(idx==1){
			agree1View = true;
		}else if(idx==2){
			agree2View = true;
		}else if(idx==3){
			agree3View = true;
		}
	}

</script>

<Header/>

<div class="inner-wrap">
	<h1>약관 동의</h1>

	<ul>
		<li>
			<div><input type="checkbox" id="agree1" bind:checked={formData.agree1}><label for="agree1">이용약관 동의 (필수)</label></div>
			<div><button type='button' class="small" on:click={handleAgreeView} idx={1}>내용보기</button></div>
		</li>
		<li>
			<div><input type="checkbox" id="agree2"  bind:checked={formData.agree2}><label for="agree2">개인정보 수집 및 이용 동의 (필수)</label></div>
			<div><button type='button' class="small" on:click={handleAgreeView} idx={2}>내용보기</button></div>
		</li>
		<li>
			<div><input type="checkbox" id="agree3"  bind:checked={formData.agree3}><label for="agree3">개인정보 위탁처리 동의 (필수)</label></div>
			<div><button type='button' class="small" on:click={handleAgreeView} idx={3}>내용보기</button></div>
		</li>
		<li>
			<div><input type="checkbox" id="agree4"  bind:checked={formData.agree4}><label for="agree4">만 14세 이상입니다. (필수)</label></div>
			<div></div>
		</li>
	</ul>

	<div class="box">
		<h3>예약 전 유의사항</h3>
		<Precautions1></Precautions1>
		<div class='checkbox'><input type="checkbox" id="agree5"  bind:checked={formData.agree5}><label for="agree5">네, 인지하였습니다.</label></div>
	</div>

	<div class="box">
		<h3>발송 물품 유의사항</h3>
		<Precautions2></Precautions2>
		<div class='checkbox'><input type="checkbox" id="agree6"  bind:checked={formData.agree6}><label for="agree6">네, 인지하였습니다.</label></div>
	</div>

	<div class="box">
		<h3>발송 시 라벨 부착 주의</h3>
		<Precautions3></Precautions3>
		<div class='checkbox'><input type="checkbox" id="agree7"  bind:checked={formData.agree7}><label for="agree7">네, 인지하였습니다.</label></div>
	</div>

	<div class="box">
		<h3>파손면책 동의</h3>
		<p>※ 파손면책이란?
			배송 중 포장부실로 인한 상품의 고장/파손에 대하여 택배사의 배상책임을 묻지 않겠다는 고객의 확인입니다.</p>
		<div>파손면책(택배사 책임없음 인정)시 접수 가능합니다.</div>
		<div>파손면책에 동의하시겠습니까?</div>
		
		<div class='checkbox'><input type="checkbox" id="agree8"  bind:checked={formData.agree8}><label for="agree8">네, 동의합니다.</label></div>
	</div>

	<div class="button-area">
		<button type="button" on:click={handleAgree}>접수하기</button>
	</div>
</div>

{#if agree1View}
	<Agree {handleMask}></Agree>
	<div class='mask' on:click={handleMask}></div>
{/if}

{#if agree2View}
	<Personal {handleMask}></Personal>
	<div class='mask' on:click={handleMask}></div>
{/if}

{#if agree3View}
	<Entrust {handleMask}></Entrust>
	<div class='mask' on:click={handleMask}></div>
{/if}


<style lang="scss">
	.inner-wrap{
		margin-top:95px;
		margin-bottom:66px;
	}

	h1{
		font-size:var(--font-size-big-big);
		text-align: center;
	}

	.button-area{
		text-align: center;
		margin-top:67px;
	}

	.small{
		font-size: 18px;
		height: 36px;
		line-height: 0;
		width: 93px;
		min-width: 0px;
		padding:0;
		
	}

	ul{
		display: inline-block;
		width:100%;
		border-top:3px solid var(--black);
		margin-top:45px;
	}

	li{
		display: flex;
		justify-content: space-between;
		align-items: center;
		border-bottom:2px solid var(--border-color-gray);
		padding:18.5px 0px;
		min-height: 40px;

		label{
			cursor: pointer;
		}
	}

	.mask{
		position: fixed;
		width:100vw;
		height: 100vh;
		background: rgba(1,1,1,0.9);
		z-index: 2000;
		top:0;
		left:0;
	}

	.box{
		margin-top:80px;

		p{
			white-space:pre-line;
			padding:10px;
			background:antiquewhite;
			line-height: 1.32;
			margin-top:20px;		
		}

		div:not(.checkbox){
			margin-top:10px;
		}
	}

	

	h3{
		border-bottom: 3px solid var(--black);
		padding-bottom: 18.5px;
		font-size: var(--font-size-normal);
	}

	.checkbox{
		margin-top:18.5px;
		border-top:2px solid var(--border-color-gray);
		padding-top:18.5px;
	}

	label{
		cursor: pointer;
	}


	
	

	@media only screen and (max-width:425px){
		.inner-wrap{
			margin-top:53px;
			margin-bottom:122px;
		}

		h1{
			font-weight: 500;
		}

		.button-area{
			text-align: center;
			margin-top:45px;
		}

		button{
			margin:0;
			margin-bottom:10px;
		}

		.small{
			transform:translateY(5px);
		}

	}
</style>