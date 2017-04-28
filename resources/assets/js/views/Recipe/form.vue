<template>
	<div class="product__show">
		<div class="product__header">
			<h3>{{action}} Product</h3>
			<div>
				<button class="btn btn__primary" @click="save" :disabled="isProcessing">Save</button>
				<button class="btn" @click="$router.back()" :disabled="isProcessing">Cancel</button>
			</div>
		</div>
		<div class="product__row">
			<div class="product__image">
				<div class="product__box">
					<image-upload v-model="form.image"></image-upload>
					<small class="error__control" v-if="error.image">{{error.image[0]}}</small>
				</div>
			</div>
			<div class="product__details">
				<div class="product__details_inner">
					<div class="form__group">
					    <label>Name</label>
					    <input type="text" class="form__control" v-model="form.name">
					    <small class="error__control" v-if="error.name">{{error.name[0]}}</small>
					</div>
					<div class="form__group">
					    <label>Description</label>
					    <textarea class="form__control form__description" v-model="form.description"></textarea>
					    <small class="error__control" v-if="error.description">{{error.description[0]}}</small>
					</div>
				</div>
			</div>
		</div>
		<div class="product__row">
			<div class="product__details">
				<div class="product__box">
					<h3 class="product__sub_title">Details</h3>
					<div v-for="(detail, index) in form.details" class="product__form">
						<input type="text" class="form__control" v-model="detail.name"
							:class="[error[`details.${index}.name`] ? 'error__bg' : '']">
						<input type="text" class="form__control form__qty" v-model="detail.qty"
							:class="[error[`details.${index}.qty`] ? 'error__bg' : '']">
						<button @click="remove('details', index)" class="btn btn__danger">&times;</button>
					</div>
					<button @click="addDetail" class="btn">Add Detail</button>
				</div>
			</div>
			<div class="product__explains">
				<div class="product__explains_inner">
					<h3 class="product__sub_title">explains</h3>
					<div v-for="(explain, index) in form.explains" class="product__form">
						<textarea class="form__control form__margin" v-model="explain.description"
							:class="[error[`explains.${index}.description`] ? 'error__bg' : '']"
							></textarea>
						<button @click="remove('explains', index)" class="btn btn__danger">&times;</button>
					</div>
					<button @click="addExplain" class="btn">Add Explain</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import Vue from 'vue'
	import Flash from '../../helpers/flash'
	import { get, post } from '../../helpers/api'
	import { toMulipartedForm } from '../../helpers/form'
	import ImageUpload from '../../components/ImageUpload.vue'

	export default {
		components: {
			ImageUpload
		},
		data() {
			return {
				form: {
					details: [],
					explains: []
				},
				error: {},
				isProcessing: false,
				initializeURL: `/api/products/create`,
				storeURL: `/api/products`,
				action: 'Create'
			}
		},
		created() {
			if(this.$route.meta.mode === 'edit') {
				this.initializeURL = `/api/products/${this.$route.params.id}/edit`
				this.storeURL = `/api/products/${this.$route.params.id}?_method=PUT`
				this.action = 'Update'
			}
			get(this.initializeURL)
				.then((res) => {
					Vue.set(this.$data, 'form', res.data.form)
				})
		},
		methods: {
			save() {
				const form = toMulipartedForm(this.form, this.$route.meta.mode)
				post(this.storeURL, form)
				    .then((res) => {
				        if(res.data.saved) {
				            Flash.setSuccess(res.data.message)
				            this.$router.push(`/products/${res.data.id}`)
				        }
				        this.isProcessing = false
				    })
				    .catch((err) => {
				        if(err.response.status === 422) {
				            this.error = err.response.data
				        }
				        this.isProcessing = false
				    })
			},
			addExplain() {
				this.form.explains.push({
					description: ''
				})
			},
			addDetail() {
				this.form.details.push({
					name: '',
					qty: ''
				})
			},
			remove(type, index) {
				if(this.form[type].length > 1) {
					this.form[type].splice(index, 1)
				}
			}
		}
	}
</script>
