<template>
  <Card :bordered="false" :dis-hover="true">
    <Row>
      <Col span="20">
        <Button type="primary" @click="AddVehicleStatus()">Add vehicles</Button>
      </Col>
      <Col span="4">
        <Input
          search
          enter-button="Search"
          placeholder="Enter keywords..."
          @on-search="GetVehicleList"
          v-model="vehicle_search_keywords"
        />
      </Col>
    </Row>
    <div class="page-table">
      <Table
        :columns="vehicle_column"
        :data="vehicle_data"
        no-data-text="NO DATA"
      >
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id + 1 }}</strong>
        </template>
        <template slot-scope="{ index }" slot="Person">
          <Button type="info" size="small" @click="vehiclesStatus(index)"
            >View</Button
          >
        </template>
        <template slot-scope="{ index }" slot="action">
          <Button type="success" size="small" @click="editStatus(index)"
            >Edit</Button
          >
          <Button type="error" size="small" @click="DeleteVehicle(index)"
            >Delete</Button
          >
        </template>
      </Table>
      <Card :bordered="false" :dis-hover="true">
        <Page
          v-if="content_total != 0"
          :total="content_total"
          :page-size="page_size"
          @on-change="pageChange"
        />
      </Card>
    </div>
    <Modal
      v-model="add_vehicle_status"
      title="Add vehicles"
      @on-ok="AddVehicle()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formVehicle1" :model="formVehicle1" :label-width="80">
        <FormItem label="VehicleID ">
          <Input v-model="formVehicle1.Vehicle_id" />
        </FormItem>
        <FormItem label="Make ">
          <Input v-model="formVehicle1.Vehicle_make" />
        </FormItem>
        <FormItem label="Model">
          <Input v-model="formVehicle1.Vehicle_model" />
        </FormItem>
        <FormItem label="Color">
          <Input v-model="formVehicle1.Vehicle_color" />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="edit_vehicle_status"
      title="Edit"
      @on-ok="EditVehicle()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formVehicle" :model="formVehicle" :label-width="80">
        <FormItem label="VehicleID ">
          <Input v-model="formVehicle.Vehicle_id" />
        </FormItem>
        <FormItem label="Make ">
          <Input v-model="formVehicle.Vehicle_make" />
        </FormItem>
        <FormItem label="Model">
          <Input v-model="formVehicle.Vehicle_model" />
        </FormItem>
        <FormItem label="Color">
          <Input v-model="formVehicle.Vehicle_color" />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="owner_vehicle_status"
      title="Owner"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Card v-if="formOwner.isnull" :bordered="false" :dis-hover="true">
        <div style="text-align: center">
          <h3>No Owner</h3>
        </div>
      </Card>
      <Form ref="formOwner" :model="formOwner" :label-width="80" v-else>
        <FormItem label="Name">
          <Input v-model="formOwner.Person_name" readonly />
        </FormItem>
        <FormItem label="Address">
          <Input v-model="formOwner.Person_address" readonly />
        </FormItem>
        <FormItem label="Birth">
          <Input v-model="formOwner.Person_birth" readonly />
        </FormItem>
        <FormItem label="License">
          <Input v-model="formOwner.Person_license" readonly />
        </FormItem>
        <FormItem label="Points">
          <Input v-model="formOwner.Person_points" readonly />
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
       * search keyword
       */
      vehicle_search_keywords: "",
      /**
       * paging
       */
      current: 1, //What page is it on
      page_size: 10, //Each page has several pieces of data
      content_total: 20, //How many pieces of data are there
      /**
       * Vehicle list fields and data
       */
      vehicle_column: [
        {
          title: "ID",
          key: "id",
          slot: "id",
        },
        {
          title: "VehicleID",
          key: "Vehicle_id",
        },
        {
          title: "Make",
          key: "Vehicle_make",
        },
        {
          title: "Model",
          key: "Vehicle_model",
        },
        {
          title: "Color",
          key: "Vehicle_color",
        },
        {
          title: "Owner",
          slot: "Person",
        },
        {
          title: "Action",
          slot: "action",
          align: "center",
        },
      ],
      vehicle_data: [
        // {
        //   id: 0,
        //   Vehicle_id: 1,
        //   Vehicle_make: "",
        //   Vehicle_model: "",
        //   Vehicle_color: "",
        //   Person_id: "",
        // },
      ],
      /**
       * Vehicle information data structure
       */
      formVehicle: {
        Vehicle_id: "",
        Vehicle_make: "",
        Vehicle_model: "",
        Vehicle_color: "",
        Person_id: "",
      },
      formVehicle1: {
        Vehicle_id: "",
        Vehicle_make: "",
        Vehicle_model: "",
        Vehicle_color: "",
        Person_id: "",
      },
      /**
       * The owner information data structure
       */
      formOwner: {
        Person_name: "",
        Person_address: "",
        Person_birth: "",
        Person_license: "",
        Person_points: "",
        isnull: false,
      },
      /**
       * Loading animation control
       */
      /**
       * Dialog state control
       */
      add_vehicle_status: false, //Add vehicles MessageBox
      edit_vehicle_status: false, //Edit vehicle MessageBox
      owner_vehicle_status: false, //View owner MessageBox
    };
  },
  methods: {
    /**
     * Paging changes the triggering function
     */
    pageChange(value) {
      var that = this;
      that.current = value; //private int current page
      that.GetVehicleList();
    },
    /**
     * AddVehicleStatus
     */
    AddVehicleStatus() {
      var that = this;
      that.add_vehicle_status = true;
      (that.formVehicle1.Vehicle_id = ""),
        (that.formVehicle1.Vehicle_make = ""),
        (that.formVehicle1.Vehicle_model = ""),
        (that.formVehicle1.Vehicle_color = ""),
        (that.formVehicle1.Person_id = "");
    },
    /**
     * Edit Vehicle information pop-up box
     */
    editStatus(index) {
      var that = this;
      that.edit_vehicle_status = true;

      that.formVehicle = that.vehicle_data[index];
    },
    /**
     * View vehicle MessageBox
     */
    vehiclesStatus(index) {
      var that = this;
      that.owner_vehicle_status = true;
      that.GetOwnerInfo(that.vehicle_data[index]["Vehicle_id"]);
    },
    /**
     * Add vehicles information
     */
    AddVehicle() {
      var that = this;
      var data = {
        Vehicle_id: that.formVehicle1.Vehicle_id,
        Vehicle_make: that.formVehicle1.Vehicle_make,
        Vehicle_model: that.formVehicle1.Vehicle_model,
        Vehicle_color: that.formVehicle1.Vehicle_color,
      };
      that.$axios
        .post("/api.php?function=AddVehicle", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetVehicleList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Delete vehicle information
     */
    DeleteVehicle(index) {
      var that = this;
      var data = {
        Vehicle_id: that.vehicle_data[index]["Vehicle_id"],
      };
      that.$Modal.confirm({
        title: "Warning",
        okText: "Confirm",
        cancelText: "Cancel",
        content: "Are you sure to delete this record？",
        onOk: () => {
          that.$axios
            .post("/api.php?function=DeleteVehicle", data)
            .then(function (response) {
              var result = response.data;
              if (result.status == 1) {
                that.$Message.success(result.message);
                that.GetVehicleList();
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
     * Edit vehicle information
     */
    EditVehicle() {
      var that = this;
      var data = {
        Vehicle_id: that.formVehicle.Vehicle_id,
        Vehicle_make: that.formVehicle.Vehicle_make,
        Vehicle_model: that.formVehicle.Vehicle_model,
        Vehicle_color: that.formVehicle.Vehicle_color,
      };
      that.$axios
        .post("/api.php?function=EditVehicle", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetVehicleList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Get a list of vehicles
     */
    GetVehicleList() {
      var that = this;
      var data = {
        pageSize: that.page_size,
        currentPage: that.current,
        vehicle_search_keywords: that.vehicle_search_keywords,
      };
      that.$axios
        .post("/api.php?function=GetVehicleList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.vehicle_data = result.data;
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
     * Obtain owner information
     */
    GetOwnerInfo(Vehicle_id) {
      var that = this;
      var data = {
        Vehicle_id: Vehicle_id,
      };
      that.$axios
        .post("/api.php?function=GetOwnerInfo", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.formOwner = result.data;
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
    that.GetVehicleList();
  },
};
</script>