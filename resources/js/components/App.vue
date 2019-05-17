<template>
    <div class="container">
        <el-row class="filter-group" :gutter="20" v-for="(filterGroup, index) in filterGroups" :key="index">
            <div class="group-connector" v-if="index">
                <el-select @change="getBooks" v-model="filterGroup.or">
                    <el-option label="And" :value="false"></el-option>
                    <el-option label="Or" :value="true"></el-option>
                </el-select>
            </div>

            <el-col :span="10">
                <el-input @input="getBooks" placeholder="Filter by title" v-model="filterGroup.filters.title"></el-input>
            </el-col>

            <el-col :span="3">
                <el-select @change="getBooks" v-model="filterGroup.filters.or">
                    <el-option label="And" :value="false"></el-option>
                    <el-option label="Or" :value="true"></el-option>
                </el-select>
            </el-col>

            <el-col :span="10">
                <el-input @input="getBooks" placeholder="Filter by author" v-model="filterGroup.filters.author"></el-input>
            </el-col>

            <el-col :span="1">
                <el-button v-if="!index"
                           @click="addFilterGroup"
                           type="primary"
                           icon="el-icon-plus">
                </el-button>
                <el-button v-else @click="removeFilterGroup(index)" type="primary" icon="el-icon-minus"></el-button>
            </el-col>
        </el-row>

        <el-table v-loading="dataLoading" :data="books" @sort-change="sortingChanged">
            <el-table-column prop="title" label="Title" sortable="custom"></el-table-column>
            <el-table-column prop="author" label="Author" sortable="custom"></el-table-column>
            <el-table-column prop="updated_at" label="Updated at"></el-table-column>
            <el-table-column prop="created_at" label="Created at"></el-table-column>
            <el-table-column label="Operations" align="right" width="100px">
                <template slot-scope="scope">
                    <el-button @click="editingBook = scope.row" type="primary" icon="el-icon-edit" circle size="small"></el-button>
                    <el-button @click="deleteBook(scope.row)" type="danger" icon="el-icon-delete" circle size="small"></el-button>
                </template>
            </el-table-column>
        </el-table>

        <updating-book-dialog :book="editingBook" @closed="editingBook = null" @updated="getBooks"></updating-book-dialog>
    </div>
</template>

<script>
    import ApiBridge from './../api-bridge';
    import UpdatingBookDialog from './UpdatingBookDialog';

    export default {
        components: {
            UpdatingBookDialog
        },

        data() {
            return {
                books: [],
                dataLoading: false,
                sorting: null,
                editingBook: null,

                filterGroups: [
                    {
                        or: false,
                        filters: {
                            or: false,
                            title: null,
                            author: null
                        },
                    }
                ]
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

                params['filter_groups'] = this.getParsedFilterGroups();

                this.dataLoading = true;
                ApiBridge.books.getAll(params).then(({data}) => {
                    this.dataLoading = false;
                    this.books = data.books;
                }).catch((error) => {
                    this.dataLoading = false;
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
                    this.dataLoading = true;
                    ApiBridge.books.delete(book.id).then(() => {
                        this.dataLoading = false;
                        this.getBooks();
                    }).catch((error) => {
                        this.dataLoading = false;
                        console.log(error);
                        this.$notify.error({
                            title: 'Error',
                            message: 'Something went wrong'
                        });
                    });
                }).catch(() => {});
            },

            addFilterGroup() {
                this.filterGroups.push({
                    or: false,
                    filters: {
                        or: false,
                        title: null,
                        author: null
                    },
                });
            },

            removeFilterGroup(index) {
                this.filterGroups.splice(index, 1);
            },

            getParsedFilterGroups() {
                let filterGroups = [];
                for (let i in this.filterGroups) {
                    let data = this.filterGroups[i];

                    let or = data.or;
                    let filters = [];

                    if (data.filters.title) {
                        filters.push({
                            or: data.filters.or,
                            field: 'title',
                            value: data.filters.title,
                            operator: 'like'
                        });
                    }

                    if (data.filters.author) {
                        filters.push({
                            or: data.filters.or,
                            field: 'author',
                            value: data.filters.author,
                            operator: 'like'
                        });
                    }

                    let filterGroup = {or, filters};

                    filterGroups.push(filterGroup);
                }

                return filterGroups;
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

    .filter-group {
        margin: 10px 0;
    }

    .group-connector {
        position: absolute;
        left: -80px;
        width: 80px;
        top: -28px;
    }
</style>
