<template>
    <div class="container" v-loading="pageLoading">
        <el-table :data="books" @sort-change="sortingChanged">
            <el-table-column prop="title" label="Title" sortable="custom"></el-table-column>
            <el-table-column prop="author" label="Author" sortable="custom"></el-table-column>
            <el-table-column prop="updated_at" label="Updated at"></el-table-column>
            <el-table-column prop="created_at" label="Created at"></el-table-column>
            <el-table-column label="Operations" align="right" width="100px">
                <template slot-scope="scope">
                    <el-button @click="deleteBook(scope.row)" type="danger" icon="el-icon-delete" circle size="small"></el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    import ApiBridge from './../api-bridge';

    export default {
        data() {
            return {
                books: [],
                pageLoading: false,
                sorting: null
            }
        },

        mounted() {
            this.getBooks();
        },

        methods: {
            getBooks() {
                let params = {};

                if (this.sorting) {
                    params['sort'] = [this.sorting];
                }

                ApiBridge.books.getAll(params).then(({data}) => {
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
            },

            sortingChanged(event) {
                if (event.prop) {
                    let order = null;
                    switch(event.order) {
                        case "descending": order = 'desc'; break;
                        case "ascending": order = 'asc';
                    }

                    this.sorting = {
                        "field": event.prop,
                        "direction": order
                    };
                } else {
                    this.sorting = null;
                }

                this.getBooks();
            },

            deleteBook(book) {
                this.$confirm('Are you sure you want to delete - ' + book.title, {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                    this.pageLoading = true;
                    ApiBridge.books.delete(book.id).then(() => {
                        this.pageLoading = false;
                        this.getBooks();
                    }).catch((error) => {
                        this.pageLoading = false;
                        console.log(error);
                        this.$notify.error({
                            title: 'Error',
                            message: 'Something went wrong'
                        });
                    });
                }).catch(() => {});
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
