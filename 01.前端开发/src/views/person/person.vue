<template>
  <Card :bordered="false" :dis-hover="true">
    <Row>
      <Col span="20">
        <Button type="primary" @click="AddPersonStatus()"
          >Add the person</Button
        >
      </Col>
      <Col span="4">
        <Input
          search
          enter-button="Search"
          placeholder="Enter keywords..."
          @on-search="GetPersonList"
          v-model="person_search_keywords"
        />
      </Col>
    </Row>
    <div class="page-table">
      <Table
        :columns="person_column"
        :data="person_data"
        no-data-text="NO DATA"
      >
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id + 1 }}</strong>
        </template>
        <template slot-scope="{ index }" slot="Vehicles">
          <Button type="info" size="small" @click="vehiclesStatus(index)"
            >View</Button
          >
        </template>
        <template slot-scope="{ index }" slot="action">
          <Button type="success" size="small" @click="editStatus(index)"
            >Edit</Button
          >
          <Button type="error" size="small" @click="DeletePerson(index)"
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
      v-model="add_person_status"
      title="Add the person"
      @on-ok="AddPerson()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formPerson1" :model="formPerson1" :label-width="80">
        <FormItem label="Name">
          <Input v-model="formPerson1.Person_name" />
        </FormItem>
        <FormItem label="Address">
          <Input v-model="formPerson1.Person_address" />
        </FormItem>
        <FormItem label="Birth">
          <Input v-model="formPerson1.Person_birth" />
        </FormItem>
        <FormItem label="Licence">
          <Input v-model="formPerson1.Person_license" />
        </FormItem>
        <FormItem label="Points">
          <Input v-model="formPerson1.Person_points" />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="edit_person_status"
      title="Edit"
      @on-ok="EditPerson()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formPerson" :model="formPerson" :label-width="80">
        <FormItem label="Name">
          <Input v-model="formPerson.Person_name" />
        </FormItem>
        <FormItem label="Address">
          <Input v-model="formPerson.Person_address" />
        </FormItem>
        <FormItem label="Birth">
          <Input v-model="formPerson.Person_birth" />
        </FormItem>
        <FormItem label="Licence">
          <Input v-model="formPerson.Person_license" />
        </FormItem>
        <FormItem label="Points">
          <Input v-model="formPerson.Person_points" />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="view_person_vehicle_status"
      title="Vehicles"
      width="650"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Button
        type="primary"
        size="default"
        style="margin-bottom: 16px"
        @click="AddPersonVehicleStatus()"
        >Add a vehicle</Button
      >
      <Table
        height="400"
        border
        :columns="person_vehicle_column"
        :data="person_vehicle_data"
        no-data-text="NO DATA"
      >
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id + 1 }}</strong>
        </template>
        <template slot-scope="{ index }" slot="action">
          <Button @click="DeletePersonVehicle(index)">Delete</Button>
        </template>
      </Table>
    </Modal>
    <Modal
      v-model="add_person_vehicle_status"
      title="Vehicle-Add"
      width="650"
      @on-ok="SetPersonVehicleList()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="447"
        border
        :columns="part_person_vehicle_column"
        :data="part_person_vehicle_data"
        @on-selection-change="SelectChange"
        no-data-text="NO DATA"
      >
      </Table>
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
      person_search_keywords: "",
      /**
       * The owner information data structure
       */
      formPerson: {
        Person_id: "",
        Person_name: "",
        Person_address: "",
        Person_birth: "",
        Person_license: "",
        Person_points: "",
      },
      formPerson1: {
        Person_id: "",
        Person_name: "",
        Person_address: "",
        Person_birth: "",
        Person_license: "",
        Person_points: "",
      },
      /**
       * Loading animation control
       */
      /**
       * Dialog state control
       */
      add_person_status: false, //Add owner information dialog box
      edit_person_status: false, //Edit the owner information dialog box
      view_person_vehicle_status: false, //View vehicle owner information dialog box
      add_person_vehicle_status: false, //Add a vehicle information dialog box for the owner
      /**
       * Temporarily stores selected information
       */
      temp: {
        Person_id: "",
      },
      /**
       *  paging
       */
      current: 1, //What page is it on
      page_size: 10, //Each page has several pieces of data
      content_total: 20, //How many pieces of data are there
      /**
       * The person list
       */
      person_column: [
        {
          title: "ID",
          key: "id",
          slot: "id",
        },
        {
          title: "Name",
          key: "Person_name",
        },
        {
          title: "Address",
          key: "Person_address",
        },
        {
          title: "Birth",
          key: "Person_birth",
        },
        {
          title: "Licence",
          key: "Person_license",
        },
        {
          title: "Points",
          key: "Person_points",
        },
        {
          title: "Vehicles",
          slot: "Vehicles",
        },
        {
          title: "Action",
          slot: "action",
          align: "center",
        },
      ],
      person_data: [
        // {
        //   id: 0,
        //   Person_id: "",
        //   Person_name: "Tom",
        //   Person_address: "address",
        //   Person_birth: "2000-10-22",
        //   Person_license: "YUWEHUU21JI2J1U8",
        //   Person_points: "12",
        // },
      ],
      /**
       * The owner owns the list of vehicles
       */
      person_vehicle_column: [
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
          title: "Action",
          slot: "action",
          align: "center",
        },
      ],
      person_vehicle_data: [
        // {
        //   id: 0,
        //   Vehicle_id: "sdjfklsjdlfksjkdlfj",
        //   Vehicle_make: "",
        //   Vehicle_model: "",
        //   Vehicle_color: "",
        // },
      ],
      /**
       * Owners add vehicles to the list
       */
      part_person_vehicle_column: [
        {
          type: "selection",
          // width: 60,
          align: "center",
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
      ],
      part_person_vehicle_data: [
        // {
        //   Vehicle_id: "7777",
        //   Vehicle_make: "",
        //   Vehicle_model: "",
        //   Vehicle_color: "",
        // },
      ],
      /**
       * Multi-check the vehicle information data structure
       */
      selected_vehicle_list: {},
    };
  },
  methods: {
    /**
     * Paging changes the triggering function
     */
    pageChange(value) {
      var that = this;
      that.current = value; //private int current page
      that.GetPersonList();
    },
    /**
     * AddPersonStatus
     */
    AddPersonStatus() {
      var that = this;
      that.add_person_status = true;
      that.formPerson1.Person_id = "";
      (that.formPerson1.Person_name = ""),
        (that.formPerson1.Person_address = ""),
        (that.formPerson1.Person_birth = ""),
        (that.formPerson1.Person_license = ""),
        (that.formPerson1.Person_points = "");
    },
    /**
     * Edit the owner information pop-up box
     */
    editStatus(index) {
      var that = this;
      that.edit_person_status = true;

      that.formPerson = that.person_data[index];
    },
    /**
     * View the owner's vehicle information pop-up box
     */
    vehiclesStatus(index) {
      var that = this;
      that.view_person_vehicle_status = true;
      that.temp.Person_id = that.person_data[index]["Person_id"];
      that.GetPersonVehicleList(that.temp.Person_id);
    },
    /**
     * Add owner information
     */
    AddPerson() {
      var that = this;
      var data = {
        Person_name: that.formPerson1.Person_name,
        Person_address: that.formPerson1.Person_address,
        Person_birth: that.formPerson1.Person_birth,
        Person_license: that.formPerson1.Person_license,
        Person_points: that.formPerson1.Person_points,
      };
      that.$axios
        .post("/api.php?function=AddPerson", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetPersonList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Delete owner information
     */
    DeletePerson(index) {
      var that = this;
      var data = {
        Person_id: that.person_data[index]["Person_id"],
      };
      that.$Modal.confirm({
        title: "Warning",
        okText: "Confirm",
        cancelText: "Cancel",
        content: "Are you sure to delete this record？",
        onOk: () => {
          that.$axios
            .post("/api.php?function=DeletePerson", data)
            .then(function (response) {
              var result = response.data;
              if (result.status == 1) {
                that.$Message.success(result.message);
                that.GetPersonList();
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
     * Edit owner information
     */
    EditPerson() {
      var that = this;
      var data = {
        Person_id: that.formPerson.Person_id,
        Person_name: that.formPerson.Person_name,
        Person_address: that.formPerson.Person_address,
        Person_birth: that.formPerson.Person_birth,
        Person_license: that.formPerson.Person_license,
        Person_points: that.formPerson.Person_points,
      };
      that.$axios
        .post("/api.php?function=EditPerson", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetPersonList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Get a list of owners
     */
    GetPersonList() {
      var that = this;
      var data = {
        pageSize: that.page_size,
        currentPage: that.current,
        person_search_keywords: that.person_search_keywords,
      };
      that.$axios
        .post("/api.php?function=GetPersonList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.person_data = result.data;
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
     * Delete the information that the owner owns the vehicle
     */
    DeletePersonVehicle(index) {
      var that = this;
      var data = {
        Vehicle_id: that.person_vehicle_data[index]["Vehicle_id"],
      };
      that.$Modal.confirm({
        title: "Warning",
        okText: "Confirm",
        cancelText: "Cancel",
        content:
          "This operation will only remove the ownership relationship with the vehicle, and the vehicle information will still be retained in the system. Are you sure to continue？",
        onOk: () => {
          that.$axios
            .post("/api.php?function=DeletePersonVehicle", data)
            .then(function (response) {
              var result = response.data;
              if (result.status == 1) {
                that.$Message.success(result.message);
                that.GetPersonVehicleList(that.temp.Person_id);
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
     * Gets a list of vehicles owned by the owner
     */
    GetPersonVehicleList(Person_id) {
      var that = this;
      var data = {
        Person_id: Person_id,
      };
      that.$axios
        .post("/api.php?function=GetPersonVehicleList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.person_vehicle_data = result.data;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Owners add vehicle pop-up box
     */
    AddPersonVehicleStatus() {
      var that = this;
      that.add_person_vehicle_status = true;
      that.GetPartVehicleList();
    },
    /**
     * Gets a list of vehicles that have no owner
     */
    GetPartVehicleList() {
      var that = this;
      var data = {};
      that.$axios
        .post("/api.php?function=GetPartVehicleList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.part_person_vehicle_data = result.data;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Multiple trigger functions are selected
     */
    SelectChange(selection) {
      var that = this;
      that.selected_vehicle_list = selection;
    },
    /**
     * Add a vehicle for the owner
     */
    SetPersonVehicleList() {
      var that = this;
      var data = {
        Person_id: that.temp.Person_id,
        selected_vehicle_list: that.selected_vehicle_list,
      };
      that.$axios
        .post("/api.php?function=SetPersonVehicleList", data)
        .then(function (response) {
          console.log(response.data);
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetPersonVehicleList(that.temp.Person_id);
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
    that.GetPersonList();
  },
};
</script>