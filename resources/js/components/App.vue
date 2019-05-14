<template>
    <div class="container" v-loading="pageLoading">
        <el-table :data="books">
            <el-table-column prop="title" label="Title" sortable></el-table-column>
            <el-table-column prop="author" label="Author" sortable></el-table-column>
            <el-table-column prop="updated_at" label="Updated at"></el-table-column>
            <el-table-column prop="created_at" label="Created at"></el-table-column>
        </el-table>
    </div>
</template>

<script>
    import ApiBridge from './../api-bridge';

    export default {
        data() {
            return {
                books: [],
                pageLoading: false
            }
        },

        mounted() {
            this.getBooks();
        },

        methods: {
            getBooks() {
                ApiBridge.books.getAll().then(({data}) => {
                    this.pageLoading = false;
                    this.books = data.books;
                }).catch((error) => {
                    this.pageLoading = false;
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

<style lang="scss">
    .container {
        width: 70%;
        margin-right: auto;
        margin-left: auto;
    }
</style>
