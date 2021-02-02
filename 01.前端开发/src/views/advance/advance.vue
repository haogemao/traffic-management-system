<template>
  <Card :bordered="false" :dis-hover="true">
    <Card
      :bordered="false"
      :dis-hover="true"
      style="width: 380px"
      v-if="!isadmin"
    >
      <Form ref="nowUser" :model="nowUser" :label-width="90">
        <FormItem label="Username">
          <Input v-model="nowUser.User_username" disabled />
        </FormItem>
        <FormItem label="Realname">
          <Input v-model="nowUser.User_name" disabled />
        </FormItem>
        <FormItem label="NewPassword">
          <Input v-model="nowUser.User_password" type="password" password />
        </FormItem>
        <FormItem label="RePassword">
          <Input v-model="nowUser.Re_user_password" type="password" password />
        </FormItem>
        <FormItem>
          <Button type="primary" @click="SetNowUserInfo()">Save</Button>
        </FormItem>
      </Form>
    </Card>
    <Row v-if="isadmin">
      <Col span="20">
        <Button type="primary" @click="AddUserStatus()">Add user</Button>
      </Col>
    </Row>
    <div class="page-table">
      <Table
        :columns="user_column"
        :data="user_data"
        no-data-text="NO DATA"
        v-if="isadmin"
      >
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id + 1 }}</strong>
        </template>
        <template slot-scope="{ index }" slot="action">
          <Button type="success" size="small" @click="editStatus(index)"
            >edit</Button
          >
          <Button type="error" size="small" @click="DeleteUser(index)"
            >delete</Button
          >
        </template>
      </Table>
      <Card :bordered="false" :dis-hover="true" v-if="isadmin">
        <Page
          v-if="content_total != 0"
          :total="content_total"
          :page-size="page_size"
          @on-change="pageChange"
        />
      </Card>
    </div>

    <Modal
      v-model="add_user_status"
      title="Add user"
      @on-ok="AddUser()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formUser1" :model="formUser1" :label-width="80">
        <FormItem label="RoleType">
          <Select v-model="formUser1.Role_id" style="width: 200px">
            <Option
              v-for="item in roleList"
              :value="item.value"
              :key="item.value"
              >{{ item.label }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="Username">
          <Input v-model="formUser1.User_username" />
        </FormItem>
        <FormItem label="Password">
          <Input v-model="formUser1.User_password" />
        </FormItem>
        <FormItem label="Realname">
          <Input v-model="formUser1.User_name" />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="edit_user_status"
      title="Edit"
      @on-ok="EditUser()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formUser" :model="formUser" :label-width="80">
        <FormItem label="RoleType">
          <Select v-model="formUser.Role_id" style="width: 200px">
            <Option
              v-for="item in roleList"
              :value="item.value"
              :key="item.value"
              >{{ item.label }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="Username">
          <Input v-model="formUser.User_username" />
        </FormItem>
        <FormItem label="Password">
          <Input v-model="formUser.User_password" />
        </FormItem>
        <FormItem label="Realname">
          <Input v-model="formUser.User_name" />
        </FormItem>
      </Form>
    </Modal>
  </Card>
</template>
<script>
export default {
  data() {
    return {
      /**
       * Whether is  administrator
       */
      isadmin: Number(window.sessionStorage.roleid) == 1,
      /**
       * User information data structure
       */
      formUser: {
        User_id: "",
        User_username: "",
        User_password: "",
        User_name: "",
        Role_name: "",
        Role_id: "2",
      },
      formUser1: {
        User_id: "",
        User_username: "",
        User_password: "",
        User_name: "",
        Role_name: "",
        Role_id: "2",
      },
      /**
       * Currently User information data structure
       */
      nowUser: {
        User_id: "",
        User_username: "",
        User_password: "",
        Re_user_password: "",
        User_name: "",
      },
      /**
       * List of role selection
       */
      roleList: [
        {
          value: "1",
          label: "Admin",
        },
        {
          value: "2",
          label: "User",
        },
      ],
      /**
       * Dialog state control
       */
      add_user_status: false, //Add  user dialog box
      edit_user_status: false, //edit  user dialog box
      /**
       * Pagination
       */
      current: 1, //which pages
      page_size: 10, //the mount data pf each page
      content_total: 20, //totally mount data
      /**
       * users list
       */
      user_column: [
        {
          title: "ID",
          key: "id",
          slot: "id",
        },
        {
          title: "Username",
          key: "User_username",
        },
        {
          title: "Password",
          key: "User_password",
        },
        {
          title: "Role",
          key: "Role_name",
        },
        {
          title: "Realname",
          key: "User_name",
        },
        {
          title: "Action",
          slot: "action",
          align: "center",
        },
      ],
      user_data: [],
    };
  },
  methods: {
    /**
     * Paging change trigger function
     */
    pageChange(value) {
      var that = this;
      that.current = value; //current page number
      that.GetUserList();
    },
    /**
     * AddUserStatus
     */
    AddUserStatus() {
      var that = this;
      that.add_user_status = true;
      that.formUser1.User_id = "";
      that.formUser1.User_username = "";
      that.formUser1.User_password = "";
      that.formUser1.User_name = "";
      that.formUser1.Role_id = "2";
    },
    /**
     * Change  current user information
     */
    SetNowUserInfo() {
      var that = this;
      var data = {
        User_password: that.nowUser.User_password,
        Re_user_password: that.nowUser.Re_user_password,
      };
      that.$axios
        .post("/api.php?function=SetNowUserInfo", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetNowUser();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Edit the user information pop-up box
     */
    editStatus(index) {
      var that = this;
      that.edit_user_status = true;

      that.formUser = that.user_data[index];
    },
    /**
     * Add user information
     */
    AddUser() {
      var that = this;
      var data = {
        Role_id: that.formUser1.Role_id,
        User_username: that.formUser1.User_username,
        User_password: that.formUser1.User_password,
        User_name: that.formUser1.User_name,
      };
      that.$axios
        .post("/api.php?function=AddUser", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetUserList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Delete user information
     */
    DeleteUser(index) {
      var that = this;
      var data = {
        User_id: that.user_data[index]["User_id"],
      };
      that.$Modal.confirm({
        title: "Warning",
        okText: "Confirm",
        cancelText: "Cancel",
        content: "Are you sure to delete this record？",
        onOk: () => {
          that.$axios
            .post("/api.php?function=DeleteUser", data)
            .then(function (response) {
              var result = response.data;
              if (result.status == 1) {
                that.$Message.success(result.message);
                that.GetUserList();
              } else {
                that.$Message.error(result.message);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        },
        onCancel: () => {},
      });
    },
    /**
     * Edit Account Info
     */
    EditUser() {
      var that = this;
      var data = {
        User_id: that.formUser.User_id,
        Role_id: that.formUser.Role_id,
        User_username: that.formUser.User_username,
        User_password: that.formUser.User_password,
        User_name: that.formUser.User_name,
      };
      that.$axios
        .post("/api.php?function=EditUser", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetUserList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Get the list of users
     */
    GetUserList() {
      var that = this;
      var data = {
        pageSize: that.page_size,
        currentPage: that.current,
      };
      that.$axios
        .post("/api.php?function=GetUserList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.user_data = result.data;
            that.content_total = result.total;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Gets the current user's individual account information
     */
    GetNowUser() {
      var that = this;
      var data = {};
      that.$axios
        .post("/api.php?function=GetNowUser", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.nowUser = result.data;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
  },
  created() {},
  mounted() {
    var that = this;
    that.GetUserList();
    that.GetNowUser();
  },
};
</script>