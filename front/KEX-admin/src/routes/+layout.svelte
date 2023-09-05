<script>
	import "../css/common.min.css";
	import Alert2 from '../components/Alert2.svelte'
	import Loding from "../components/Loding.svelte";
</script>

<div class="app">
	
	<main>
		<slot />
	</main>

</div>

<Alert2/>
<Loding/>