<template>
    <el-dialog title="Update"
               :visible.sync="showDialog"
               :before-close="close"
               width="35%"
               :close-on-click-modal="false"
               v-loading="loading">
        <el-form label-width="50px">
            <el-form-item label="Author">
                <el-input v-model="author"></el-input>
            </el-form-item>
        </el-form>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancel</el-button>
            <el-button @click="update" type="primary">Update</el-button>
        </span>
    </el-dialog>
</template>

<script>
    import ApiBridge from './../api-bridge';

    export default {
        props: ['book'],

        data() {
            return {
                showDialog: false,
                author: null,
                loading: false
            }
        },

        watch: {
            book(value) {
                if (value) {
                    this.showDialog = true;
                    this.author = value.author
                } else {
                    this.showDialog = false;
                }
            }
        },

        methods: {
            close() {
                this.$emit('closed');
                this.author = null;
            },

            update() {
                let data = {
                    'author': this.author
                };

                this.loading = true;
                ApiBridge.books.update(this.book.id, data).then(() => {
                    this.loading = false;
                    this.$emit('updated');
                    this.close();
                }).catch((error) => {
                    this.loading = false;
                    console.log(error);
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong'
                    });
                });
            }
        }
    }
</script>