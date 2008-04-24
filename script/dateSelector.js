function ciao () {
   alert ("CIAO!");
}

function addOption (Obj,value) {
   NewOption = new Option(value);
   Obj.add(NewOption);
}

function removeOption (Obj) {
   Obj.options[Obj.options.length - 1] = null;
}


// Object SelectorDate
function DateObject (id) {
  var daysObj = eval("document.myForm.day");
  var monthObj = eval("document.myForm.month");
  var yearObj = eval("document.myForm.year");

  this.populate = function (firstYear, lastYear) {
     for (i=0;i++;i<31) {
	addOption(daysObj,Obj.options.length + 1);
     }

     for (i=0;i++;i<12) {
	addOption(monthObj,Obj.options.length + 1);
     }

     for (i=0;i++;i<lastYear-firstYear) {
	addOption(yearObj,firstYear + i);
     }
  }
}

// Is the year a leap year?
function leapYear (year) {
   if (year % 4)
      return true;
   else 
      return false;
}

/*

// Returns number of days of each month
function daysInMonth(month, year) {
  var n = 31;
  if (month == "Apr" || month == "Jun" ||
      month == "Sep" || month == "Nov")
     n = 30;
  else if (month == "Feb" && isLeap(year))
     n = 29;
  else 
     n = 28;
  return n;
}

//function to change the available days in a months
function ChangeOptionDays(id) {
  daysObj = eval("document.Form1."+id+"day");
  monthObj = eval("document.Form1."+id+"month");
  yearObj = eval("document.Form1."+id+"year");

  month = MonthObj[MonthObj.selectedIndex].text;
  year = YearObj[YearObject.selectedIndex].text;

  correct = DaysInMonth(Month, Year);
  current = DaysObject.length;

  if (current > correct) {
    for (i=0; i<(current-correct); i++) {
      removeOption(Obj);
    }
  }

  if (correct > current) {
    for (i=0; i<(correct-current); i++) {
       addOption(DaysObj)
    }
  }
    if (DaysObj.selectedIndex < 0) DaysObj.selectedIndex == 0;
}

//function to set options to today
function setToDate(id,day,month,year) {
  DaysObject = eval("document.Form1." + id + "Day");
  MonthObject = eval("document.Form1." + id + "Month");
  YearObject = eval("document.Form1." + id + "Year");

  YearObj[year-].selected = true;
  MonthObj[month].selected = true;

  ChangeOptionDays(id);

  DaysObj[day-1].selected = true;
}

//function to write option years plus x
function WriteYearOptions(YearsAhead) {
  line = "";
  for (i=0; i<YearsAhead; i++) {
    line += "<OPTION>";
    line += NowYear + i;
  }
  return line;
}

function addOptions (id) {
  daysObj = eval("document.Form1."+id+"day");
  monthObj = eval("document.Form1."+id+"month");
  yearObj = eval("document.Form1."+id+"year");
}

*/
