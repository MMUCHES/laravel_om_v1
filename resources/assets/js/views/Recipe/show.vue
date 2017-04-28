<template>
	<div class="product__show">
		<div class="product__row">
			<div class="product__image">
				<div class="product__box">
					<img :src="`/images/${product.image}`" v-if="product.image">
				</div>
			</div>
			<div class="product__details">
				<div class="product__details_inner">
					<small>Submitted by: {{product.user.name}}</small>
					<h1 class="product__title">{{product.name}}</h1>
					<p class="product__description">{{product.description}}</p>
					<div v-if="authState.api_token && authState.user_id === product.user_id">
						<router-link :to="`/products/${product.id}/edit`" class="btn btn-primary">
							Edit
						</router-link>
						<button class="btn btn__danger" @click="remove" :disabled="isRemoving">Delete</button>
					</div>
				</div>
			</div>
		</div>
		<div class="product__row">
			<div class="product__details">
				<div class="product__box">
					<h3 class="product__sub_title">Details</h3>
					<ul>
						<li v-for="detail in product.details">
							<span>{{detail.name}}</span>
							<span>{{detail.qty}}</span>
						</li>
					</ul>
				</div>
			</div>
			<div class="product__explains">
				<div class="product__explains_inner">
					<h3 class="product__sub_title">Explains</h3>
					<ul>
						<li v-for="(explain, i) in product.explains">
							<p>
								<strong>Step {{i + 1}}: </strong>
								{{explain.description}}
							</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import Auth from '../../store/auth'
	import Flash from '../../helpers/flash'
	import { get, del } from '../../helpers/api'
	export default {
		data() {
			return {
				authState: Auth.state,
				isRemoving: false,
				product: {
					user: {},
					details: [],
					explains: []
				}
			}
		},
		created() {
			get(`/api/products/${this.$route.params.id}`)
				.then((res) => {
					this.product = res.data.product
				})
		},
		methods: {
			remove() {
				this.isRemoving = false
				del(`/api/products/${this.$route.params.id}`)
					.then((res) => {
						if(res.data.deleted) {
							Flash.setSuccess('You have successfully deleted product!')
							this.$router.push('/')
						}
					})
			}
		}
	}
</script>
