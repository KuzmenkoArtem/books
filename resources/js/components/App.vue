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

        <el-row class="add-new-book" :gutter="20">
            <el-col :span="11">
                <el-input v-model="newBook.title" placeholder="Title" :disabled="bookSaving"></el-input>
            </el-col>

            <el-col :span="11">
                <el-input v-model="newBook.author" placeholder="Author" :disabled="bookSaving"></el-input>
            </el-col>

            <el-col :span="2">
                <el-button @click="createBook"
                           type="primary"
                           :icon="bookSaving ? 'el-icon-loading' : 'el-icon-circle-plus-outline'">
                </el-button>
            </el-col>
        </el-row>

        <el-button @click="showExportingBookDialog = true"
                   class="download-list-btn"
                   type="primary"
                   size="big"
                   icon="el-icon-download">
            Download this list
        </el-button>

        <exporting-books-dialog :visible="showExportingBookDialog"
                                @closed="showExportingBookDialog = false"
                                :filters="parsedFilterGroups"
                                :sorting="sorting">
        </exporting-books-dialog>
        <updating-book-dialog :book="editingBook" @closed="editingBook = null" @updated="getBooks"></updating-book-dialog>
    </div>
</template>

<script>
    import ApiBridge from './../api-bridge';
    import UpdatingBookDialog from './UpdatingBookDialog';
    import ExportingBooksDialog from './ExportingBooksDialog';

    export default {
        components: {
            UpdatingBookDialog,
            ExportingBooksDialog
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
                ],

                newBook: {
                    title: null,
                    author: null
                },
                bookSaving: false,

                showExportingBookDialog: false
            }
        },

        computed: {
            parsedFilterGroups() {
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

                params['filter_groups'] = this.parsedFilterGroups;

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
                this.getBooks();
            },

            createBook() {
                this.bookSaving = true;
                ApiBridge.books.create(this.newBook).then(() => {
                    this.bookSaving = false;

                    this.newBook.author = null;
                    this.newBook.title = null;

                    this.getBooks();
                }).catch(({response}) => {
                    this.bookSaving = false;

                    if (response.status !== 422) {
                        console.log(error);
                        this.$notify.error({
                            title: 'Error',
                            message: 'Something went wrong'
                        });

                        return;
                    }

                    for (let i in response.data.errors) {
                        // timeout is for fixing an ui bug with overlaying messages on each other
                        setTimeout(() => {
                            this.$notify.error({
                                title: 'Validation error',
                                dangerouslyUseHTMLString: true,
                                message: response.data.errors[i].join('<br />')
                            });
                        }, 0);
                    }

                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .container {
        width: 70%;
        margin-right: auto;
        margin-left: auto;
        padding-bottom: 50px;
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

    .add-new-book {
        margin-top: 20px;
    }

    .download-list-btn {
        margin-top: 20px;
    }
</style>
