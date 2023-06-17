/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

import javax.swing.JApplet;
import com.java4less.textprinter.*;
import com.java4less.textprinter.ports.*;
import com.java4less.textprinter.printers.*;
import netscape.javascript.JSObject;
import org.json.JSONArray;
import org.json.JSONException;
import java.net.*;
/**
 *
 * @author phenom
 */
public class print_or extends JApplet {
    //constants
    private static final int COL_WIDTH=80;
    //variables from rtextprinter to print in text mode
    public FilePort port;
    public TextPrinter printer;
    //JSObject variables
    private JSObject win, doc;
    public JSONArray array_print_info;
    public JSONArray array_print_items;
    public void init()
    {
        win = JSObject.getWindow(this);
        doc = (JSObject) win.getMember("document");
        System.out.print("testing..");
    }

  public void start()
  {        
  //get the value of 'param' from the javascript document
      System.out.print("recognized objects..");
      Object obj1 = doc.eval("passed_info");
      Object obj2 = doc.eval("passed_items");
      Object obj3 = doc.eval("sample");
      System.out.println("passed_info= "+obj1);    //testing--to view the contents
      System.out.println("passed_items= "+obj2);
      System.out.println("sample= "+obj3);
      String obj_info = obj1.toString(); //convert JSObject to string
      String obj_items = obj2.toString();
      try
      {
        this.array_print_info = new JSONArray(obj_info);
        this.array_print_items = new JSONArray(obj_items);
      }
      catch(JSONException e){
          e.printStackTrace();
      }
  }
  
  /*public void printObjects()
  {
        int r=0, c=0, i=0;
      try 
      {  
        //Start printing...
           JobProperties job = printer.getDefaultJobProperties();
        //Start job printing
           printer.startJob(port, job);       
        //setting the text properties
           TextProperties prop = printer.getDefaultTextProperties();
           job.pitch=10;
           job.draftQuality = false;
	   TextProperties propItalic=printer.getDefaultTextProperties();
	   propItalic.italic=true;

           //Header
           
            r +=2; c=3; 
           printer.newLine();
           //print horizontal line
           printer.printHorizontalLine(r,c, 80);
           printer.newLine();
           printer.newLine();
          try{
           for(i=0; i < this.array_print_or.length(); i++)
           {

               
		//testing - edit more formatting
		printer.printString(this.array_print_or.getJSONObject(i).getString("pass_or_no"),r+i+1,c,prop);
		printer.printString(this.array_print_or.getJSONObject(i).getString("pass_or_date"),r+i+1,c,prop);
		printer.printString(this.array_print_or.getJSONObject(i).getString("pass_or_name"),r+i+1,c,prop);
			//print the array objects in the PrintOR array

		printer.printString(this.array_print_or.getJSONObject(i).getString("pass_total_amount"),r+i+1,c,prop);
           }
          }
          catch(JSONException e){
              e.printStackTrace();
          }
           //end job printing
           printer.endJob();
    }
    catch(TextPrinterException exception) {
            exception.printStackTrace();
        }
  }*/

}
