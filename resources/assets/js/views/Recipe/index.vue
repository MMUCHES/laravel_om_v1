<template>
	<div class="product__list">
		<div class="product__item" v-for="product in products">
			<router-link class="product__inner" :to="`/products/${product.id}`">
				<img :src="`/images/${product.image}`" v-if="product.image">
				<p class="product__name">{{product.name}}</p>
			</router-link>
		</div>
	</div>
</template>
<script type="text/javascript">
	import { get } from '../../helpers/api'
	export default {
		data() {
			return {
				products: []
			}
		},
		created() {
			get('/api/products')
				.then((res) => {
					this.products = res.data.products
				})
		}
	}
</script>
