'use strict';

var dbm;
var type;
var seed;

/**
  * We receive the dbmigrate dependency from dbmigrate initially.
  * This enables us to not have to rely on NODE_PATH.
  */
exports.setup = function(options, seedLink) {
  dbm = options.dbmigrate;
  type = dbm.dataType;
  seed = seedLink;
};

exports.up = function(db, callback) {
    db.addColumn('phppos_people', 'date_of_establishment', {type: 'date'}, callback);
    db.addColumn('phppos_people', 'date_of_issue', {type: 'date'}, callback);
    db.addColumn('phppos_people', 'authorized_capital', {type: 'string', length: 100}, callback);
};

exports.down = function(db, callback) {
    db.removeColumn('phppos_people', 'date_of_establishment', callback);
    db.removeColumn('phppos_people', 'date_of_issue', callback);
    db.removeColumn('phppos_people', 'authorized_capital', callback);
};

exports._meta = {
  "version": 1
};
